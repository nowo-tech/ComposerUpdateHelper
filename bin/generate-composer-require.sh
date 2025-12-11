#!/bin/sh
# generate-composer-require.sh
# Generates composer require commands (prod and dev) from "composer outdated --direct".
# Works with any PHP project (Symfony, Laravel, Yii, CodeIgniter, etc.)
#
# Usage:
#   ./generate-composer-require.sh
#   ./generate-composer-require.sh --run   # to execute the suggested commands
#
# Packages listed in generate-composer-require.ignore.txt (one per line) will be skipped.
# For Symfony projects, respects "extra.symfony.require" constraint if present.

set -eu

# Binaries
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="$(command -v composer || true)"

if [ -z "$COMPOSER_BIN" ]; then
  echo "❌ Composer is not installed or not in PATH." >&2
  exit 1
fi

if [ ! -f composer.json ]; then
  echo "❌ composer.json not found in the current directory." >&2
  exit 1
fi

RUN_FLAG="${1:-}"

# Load ignored packages from file (if exists)
IGNORE_FILE="$(dirname "$0")/generate-composer-require.ignore.txt"
IGNORED_PACKAGES=""
if [ -f "$IGNORE_FILE" ]; then
  # Read file, remove comments and empty lines
  IGNORED_PACKAGES="$(grep -v '^\s*#' "$IGNORE_FILE" | grep -v '^\s*$' | tr '\n' '|' | sed 's/|$//' || true)"
fi

# Run composer with forced timezone to avoid warnings
# and filter any line starting with "Warning:" just in case.
OUTDATED_JSON="$("$PHP_BIN" -d date.timezone=UTC "$COMPOSER_BIN" outdated --direct --format=json 2>&1 \
  | grep -v '^Warning:' || true)"

if [ -z "${OUTDATED_JSON}" ]; then
  echo "✅ No outdated direct dependencies."
  exit 0
fi

# Process JSON with PHP (also with forced timezone)
OUTPUT="$(OUTDATED_JSON="$OUTDATED_JSON" COMPOSER_BIN="$COMPOSER_BIN" PHP_BIN="$PHP_BIN" IGNORED_PACKAGES="$IGNORED_PACKAGES" "$PHP_BIN" -d date.timezone=UTC <<'PHP'
<?php
$raw = getenv('OUTDATED_JSON') ?: '';
// In case some noise got in, try to isolate the first valid JSON:
$start = strpos($raw, '{');
$end   = strrpos($raw, '}');
if ($start === false || $end === false || $end < $start) {
    // Nothing parseable
    exit(0);
}
$json = substr($raw, $start, $end - $start + 1);
$report = json_decode($json, true);
if (!$report || empty($report['installed'])) {
    exit(0);
}

$composer = json_decode(file_get_contents('composer.json'), true);
$require    = $composer['require']     ?? [];
$requireDev = $composer['require-dev'] ?? [];
$devSet = array_fill_keys(array_keys($requireDev), true);

// Load ignored packages from environment
$ignoredPackagesRaw = getenv('IGNORED_PACKAGES') ?: '';
$ignoredPackages = [];
if ($ignoredPackagesRaw) {
    $ignoredPackages = array_flip(explode('|', $ignoredPackagesRaw));
}

// Get Symfony constraint if it exists (for Symfony projects)
$symfonyConstraint = null;
if (isset($composer['extra']['symfony']['require'])) {
    $symfonyConstraint = $composer['extra']['symfony']['require'];
}

// Function to extract the base version from a constraint (e.g.: "7.4.*" -> "7.4")
function extractBaseVersion($constraint) {
    // Remove special characters and get the main numeric part
    $parts = preg_split('/[^0-9]/', $constraint, 3);
    if (count($parts) >= 2) {
        return $parts[0] . '.' . $parts[1];
    }
    return null;
}

// Function to check if a version exceeds the Symfony constraint
function shouldLimitVersion($packageName, $latestVersion, $symfonyConstraint) {
    // Only apply to Symfony packages
    if (strpos($packageName, 'symfony/') !== 0) {
        return false;
    }

    if (!$symfonyConstraint) {
        return false;
    }

    // Normalize versions (remove 'v' prefix)
    $latest = ltrim($latestVersion, 'v');
    $baseVersion = extractBaseVersion($symfonyConstraint);

    if (!$baseVersion) {
        return false;
    }

    // Extract base version from latest (e.g.: "8.0.1" -> "8.0")
    $latestBase = extractBaseVersion($latest);
    if (!$latestBase) {
        return false;
    }

    // If the base version of latest is greater than the constraint, limit
    // E.g.: latest is 8.0.x and constraint is 7.4.* -> limit
    $latestParts = explode('.', $latestBase);
    $baseParts = explode('.', $baseVersion);

    if (count($latestParts) >= 2 && count($baseParts) >= 2) {
        $latestMajor = (int)$latestParts[0];
        $latestMinor = (int)$latestParts[1];
        $baseMajor = (int)$baseParts[0];
        $baseMinor = (int)$baseParts[1];

        // If latest is a major version or a higher minor within the same major
        if ($latestMajor > $baseMajor || ($latestMajor === $baseMajor && $latestMinor > $baseMinor)) {
            return true;
        }
    }

    return false;
}

// Function to get the latest specific version that meets a constraint
function getLatestVersionInConstraint($packageName, $constraint) {
    $composerBin = getenv('COMPOSER_BIN') ?: 'composer';
    $phpBin = getenv('PHP_BIN') ?: 'php';

    // Run composer show to get available versions
    $cmd = escapeshellarg($phpBin) . ' -d date.timezone=UTC ' . escapeshellarg($composerBin) .
           ' show ' . escapeshellarg($packageName) . ' --all --format=json 2>/dev/null';

    $output = shell_exec($cmd);
    if (!$output) {
        return null;
    }

    $data = json_decode($output, true);
    if (!$data || !isset($data['versions'])) {
        return null;
    }

    // Convert constraint to pattern (e.g.: "7.4.*" -> "7.4.")
    $baseVersion = extractBaseVersion($constraint);
    if (!$baseVersion) {
        return null;
    }

    $basePrefix = $baseVersion . '.';

    // Filter versions that start with the prefix and get the latest one
    $matchingVersions = [];
    foreach ($data['versions'] as $version) {
        $normalized = ltrim($version, 'v');
        if (strpos($normalized, $basePrefix) === 0) {
            $matchingVersions[] = $normalized;
        }
    }

    if (empty($matchingVersions)) {
        return null;
    }

    // Sort versions and take the latest one
    usort($matchingVersions, 'version_compare');
    return end($matchingVersions);
}

$prod = [];
$dev  = [];
$ignoredProd = [];
$ignoredDev  = [];

foreach ($report['installed'] as $pkg) {
    if (!isset($pkg['name'])) continue;
    $name   = $pkg['name'];
    $installed = $pkg['version'] ?? null;
    $latest = $pkg['latest'] ?? null;

    // Check if package is ignored
    if (isset($ignoredPackages[$name])) {
        if ($latest) {
            $normalized = ltrim($latest, 'v');
            if (isset($devSet[$name])) {
                $ignoredDev[] = $name . ':' . $normalized;
            } else {
                $ignoredProd[] = $name . ':' . $normalized;
            }
        }
        continue;
    }

    if (!$latest) continue;

    $normalized = ltrim($latest, 'v');
    $installedNormalized = $installed ? ltrim($installed, 'v') : null;

    // If it's a Symfony package and exceeds the constraint, get the latest specific version within the constraint
    if (shouldLimitVersion($name, $latest, $symfonyConstraint)) {
        $specificVersion = getLatestVersionInConstraint($name, $symfonyConstraint);
        if ($specificVersion) {
            $constraint = $specificVersion;
        } else {
            // Fallback: use the constraint if we can't get the specific version
            $constraint = $symfonyConstraint;
        }
    } else {
        $constraint = $normalized;
    }

    // Compare installed version with the proposed one: only include if there's really an update
    if ($installedNormalized) {
        // Normalize constraint for comparison (can be "7.4.*" or "7.4.5")
        $constraintNormalized = $constraint;
        // If it's a wildcard constraint, we can't compare directly, so we include it
        if (strpos($constraint, '*') === false && strpos($constraint, '^') === false && strpos($constraint, '~') === false) {
            // It's a specific version, we can compare
            if (version_compare($installedNormalized, $constraintNormalized, '>=')) {
                // Already at that version or higher, don't include
                continue;
            }
        }
    }

    if (isset($devSet[$name])) {
        $dev[] = $name . ':' . $constraint;
    } else {
        $prod[] = $name . ':' . $constraint;
    }
}

// Output format: sections separated by markers
$output = [];

// Commands section
$commands = [];
if ($prod) $commands[] = "composer require --with-all-dependencies " . implode(' ', $prod);
if ($dev)  $commands[] = "composer require --dev --with-all-dependencies " . implode(' ', $dev);
$output[] = "---COMMANDS---";
$output[] = implode(PHP_EOL, $commands);

// Ignored packages section
$output[] = "---IGNORED_PROD---";
$output[] = implode(' ', $ignoredProd);
$output[] = "---IGNORED_DEV---";
$output[] = implode(' ', $ignoredDev);

echo implode(PHP_EOL, $output);
PHP
)"

# Filter any "Warning:" that might have slipped in from PHP (double safety)
OUTPUT="$(printf "%s\n" "$OUTPUT" | grep -v '^Warning:' || true)"

# Parse the structured output
COMMANDS="$(printf "%s\n" "$OUTPUT" | sed -n '/^---COMMANDS---$/,/^---IGNORED_PROD---$/p' | grep -v '^---' || true)"
IGNORED_PROD="$(printf "%s\n" "$OUTPUT" | sed -n '/^---IGNORED_PROD---$/,/^---IGNORED_DEV---$/p' | grep -v '^---' || true)"
IGNORED_DEV="$(printf "%s\n" "$OUTPUT" | sed -n '/^---IGNORED_DEV---$/,$p' | grep -v '^---' || true)"

# Check if there's anything to show
if [ -z "$COMMANDS" ] && [ -z "$IGNORED_PROD" ] && [ -z "$IGNORED_DEV" ]; then
  echo "✅ No outdated direct dependencies."
  exit 0
fi

# Show ignored packages if any (prod)
if [ -n "$IGNORED_PROD" ]; then
  echo "⏭️  Ignored packages (prod):"
  printf "%s\n" "$IGNORED_PROD" | tr ' ' '\n' | while read -r pkg; do
    [ -n "$pkg" ] && echo "  - $pkg"
  done
  echo ""
fi

# Show ignored packages if any (dev)
if [ -n "$IGNORED_DEV" ]; then
  echo "⏭️  Ignored packages (dev):"
  printf "%s\n" "$IGNORED_DEV" | tr ' ' '\n' | while read -r pkg; do
    [ -n "$pkg" ] && echo "  - $pkg"
  done
  echo ""
fi

if [ -z "$COMMANDS" ]; then
  echo "✅ No packages to update (all outdated packages are ignored)."
  exit 0
fi

echo "🔧 Suggested commands:"
printf "%s\n" "$COMMANDS" | sed 's/^/  /'

if [ "$RUN_FLAG" = "--run" ]; then
  echo ""
  echo "🚀 Running..."
  printf "%s\n" "$COMMANDS" | while IFS= read -r cmd; do
    [ -z "$cmd" ] && continue
    echo "→ $cmd"
    # Run each command with forced timezone to avoid warnings during installation
    sh -lc "$PHP_BIN -d date.timezone=UTC $COMPOSER_BIN $(printf '%s' "$cmd" | sed 's/^composer //')"
  done
  echo "✅ Update completed."
fi

