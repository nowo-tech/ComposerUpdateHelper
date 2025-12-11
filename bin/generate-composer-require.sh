#!/bin/sh
# generate-composer-require.sh
# Generates composer require commands (prod and dev) from "composer outdated --direct".
# Works with any PHP project (Symfony, Laravel, Yii, CodeIgniter, CakePHP, Laminas, Slim, etc.)
#
# Usage:
#   ./generate-composer-require.sh
#   ./generate-composer-require.sh --run   # to execute the suggested commands
#
# Packages listed in generate-composer-require.ignore.txt (one per line) will be skipped.
#
# Framework support:
#   - Symfony: respects "extra.symfony.require" constraint
#   - Laravel: respects laravel/framework major.minor version
#   - Yii: respects yiisoft/yii2 major.minor version
#   - CakePHP: respects cakephp/cakephp major.minor version
#   - Laminas: respects laminas/* major.minor versions
#   - CodeIgniter: respects codeigniter4/framework major.minor version
#   - Slim: respects slim/slim major.minor version

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
$allDeps = array_merge($require, $requireDev);
$devSet = array_fill_keys(array_keys($requireDev), true);

// Load ignored packages from environment
$ignoredPackagesRaw = getenv('IGNORED_PACKAGES') ?: '';
$ignoredPackages = [];
if ($ignoredPackagesRaw) {
    $ignoredPackages = array_flip(explode('|', $ignoredPackagesRaw));
}

// ============================================================================
// FRAMEWORK DETECTION AND CONSTRAINTS
// ============================================================================

// Framework configurations: prefix => core package
$frameworkConfigs = [
    'symfony' => [
        'prefix' => 'symfony/',
        'corePackage' => null, // Uses extra.symfony.require
        'extraKey' => ['extra', 'symfony', 'require'],
    ],
    'laravel' => [
        'prefix' => 'laravel/',
        'corePackage' => 'laravel/framework',
        'related' => ['illuminate/'],
    ],
    'yii' => [
        'prefix' => 'yiisoft/',
        'corePackage' => 'yiisoft/yii2',
    ],
    'cakephp' => [
        'prefix' => 'cakephp/',
        'corePackage' => 'cakephp/cakephp',
    ],
    'laminas' => [
        'prefix' => 'laminas/',
        'corePackage' => 'laminas/laminas-mvc',
        'fallbackCore' => 'laminas/laminas-servicemanager',
    ],
    'codeigniter' => [
        'prefix' => 'codeigniter4/',
        'corePackage' => 'codeigniter4/framework',
    ],
    'slim' => [
        'prefix' => 'slim/',
        'corePackage' => 'slim/slim',
    ],
];

// Detected framework constraints (prefix => base version like "7.1")
$frameworkConstraints = [];

// Function to extract the base version from a constraint or version (e.g.: "7.4.*" -> "7.4", "^8.0" -> "8.0")
function extractBaseVersion($constraint) {
    // Remove special characters and get the main numeric part
    $constraint = ltrim($constraint, '^~>=<vV');
    $parts = preg_split('/[^0-9]/', $constraint, 3);
    if (count($parts) >= 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
        return $parts[0] . '.' . $parts[1];
    }
    return null;
}

// Detect Symfony constraint from extra.symfony.require
if (isset($composer['extra']['symfony']['require'])) {
    $baseVersion = extractBaseVersion($composer['extra']['symfony']['require']);
    if ($baseVersion) {
        $frameworkConstraints['symfony/'] = $baseVersion;
    }
}

// Detect other frameworks from installed versions
foreach ($frameworkConfigs as $name => $config) {
    if ($name === 'symfony') continue; // Already handled above
    
    $prefix = $config['prefix'];
    if (isset($frameworkConstraints[$prefix])) continue;
    
    // Try core package
    $corePackage = $config['corePackage'] ?? null;
    if ($corePackage && isset($allDeps[$corePackage])) {
        $baseVersion = extractBaseVersion($allDeps[$corePackage]);
        if ($baseVersion) {
            $frameworkConstraints[$prefix] = $baseVersion;
            // Also add related prefixes (e.g., illuminate/ for Laravel)
            if (isset($config['related'])) {
                foreach ($config['related'] as $relatedPrefix) {
                    $frameworkConstraints[$relatedPrefix] = $baseVersion;
                }
            }
            continue;
        }
    }
    
    // Try fallback core package
    $fallbackCore = $config['fallbackCore'] ?? null;
    if ($fallbackCore && isset($allDeps[$fallbackCore])) {
        $baseVersion = extractBaseVersion($allDeps[$fallbackCore]);
        if ($baseVersion) {
            $frameworkConstraints[$prefix] = $baseVersion;
        }
    }
}

// Function to check if a package belongs to a framework and get its constraint
function getFrameworkConstraint($packageName, $frameworkConstraints) {
    foreach ($frameworkConstraints as $prefix => $baseVersion) {
        if (strpos($packageName, $prefix) === 0) {
            return $baseVersion;
        }
    }
    return null;
}

// Function to check if a version exceeds the framework constraint
function shouldLimitVersion($packageName, $latestVersion, $frameworkConstraints) {
    $constraintBase = getFrameworkConstraint($packageName, $frameworkConstraints);
    if (!$constraintBase) {
        return false;
    }

    // Normalize latest version
    $latest = ltrim($latestVersion, 'v');
    $latestBase = extractBaseVersion($latest);
    if (!$latestBase) {
        return false;
    }

    // Compare base versions
    $latestParts = explode('.', $latestBase);
    $baseParts = explode('.', $constraintBase);

    if (count($latestParts) >= 2 && count($baseParts) >= 2) {
        $latestMajor = (int)$latestParts[0];
        $latestMinor = (int)$latestParts[1];
        $baseMajor = (int)$baseParts[0];
        $baseMinor = (int)$baseParts[1];

        // If latest exceeds the constraint
        if ($latestMajor > $baseMajor || ($latestMajor === $baseMajor && $latestMinor > $baseMinor)) {
            return true;
        }
    }

    return false;
}

// Function to get the latest specific version that meets a constraint
function getLatestVersionInConstraint($packageName, $baseVersion) {
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

    $basePrefix = $baseVersion . '.';

    // Filter versions that start with the prefix and get the latest one
    $matchingVersions = [];
    foreach ($data['versions'] as $version) {
        $normalized = ltrim($version, 'v');
        if (strpos($normalized, $basePrefix) === 0) {
            // Exclude dev/alpha/beta/RC versions
            if (!preg_match('/(dev|alpha|beta|rc)/i', $version)) {
                $matchingVersions[] = $normalized;
            }
        }
    }

    if (empty($matchingVersions)) {
        return null;
    }

    // Sort versions and take the latest one
    usort($matchingVersions, 'version_compare');
    return end($matchingVersions);
}

// ============================================================================
// PROCESS PACKAGES
// ============================================================================

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

    // Check if this package belongs to a framework and should be limited
    if (shouldLimitVersion($name, $latest, $frameworkConstraints)) {
        $frameworkBase = getFrameworkConstraint($name, $frameworkConstraints);
        $specificVersion = getLatestVersionInConstraint($name, $frameworkBase);
        if ($specificVersion) {
            $constraint = $specificVersion;
        } else {
            // Fallback: use the base version with wildcard
            $constraint = $frameworkBase . '.*';
        }
    } else {
        $constraint = $normalized;
    }

    // Compare installed version with the proposed one: only include if there's really an update
    if ($installedNormalized) {
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

// ============================================================================
// OUTPUT
// ============================================================================

$output = [];

// Detected frameworks section
$detectedFrameworks = [];
foreach ($frameworkConstraints as $prefix => $version) {
    $detectedFrameworks[] = rtrim($prefix, '/') . ' ' . $version . '.*';
}
$output[] = "---FRAMEWORKS---";
$output[] = implode(' ', $detectedFrameworks);

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
FRAMEWORKS="$(printf "%s\n" "$OUTPUT" | sed -n '/^---FRAMEWORKS---$/,/^---COMMANDS---$/p' | grep -v '^---' || true)"
COMMANDS="$(printf "%s\n" "$OUTPUT" | sed -n '/^---COMMANDS---$/,/^---IGNORED_PROD---$/p' | grep -v '^---' || true)"
IGNORED_PROD="$(printf "%s\n" "$OUTPUT" | sed -n '/^---IGNORED_PROD---$/,/^---IGNORED_DEV---$/p' | grep -v '^---' || true)"
IGNORED_DEV="$(printf "%s\n" "$OUTPUT" | sed -n '/^---IGNORED_DEV---$/,$p' | grep -v '^---' || true)"

# Check if there's anything to show
if [ -z "$COMMANDS" ] && [ -z "$IGNORED_PROD" ] && [ -z "$IGNORED_DEV" ]; then
  echo "✅ No outdated direct dependencies."
  exit 0
fi

# Show detected frameworks
if [ -n "$FRAMEWORKS" ]; then
  echo "🔧 Detected framework constraints:"
  printf "%s\n" "$FRAMEWORKS" | tr ' ' '\n' | while read -r fw; do
    [ -n "$fw" ] && echo "  - $fw"
  done
  echo ""
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
