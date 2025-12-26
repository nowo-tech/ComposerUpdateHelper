#!/bin/sh
# generate-composer-require.sh
# Generates composer require commands (prod and dev) from "composer outdated --direct".
# Works with any PHP project (Symfony, Laravel, Yii, CodeIgniter, CakePHP, Laminas, Slim, etc.)
#
# Usage:
#   ./generate-composer-require.sh
#   ./generate-composer-require.sh --run                    # to execute the suggested commands
#   ./generate-composer-require.sh --release-detail          # show full release changelog
#   ./generate-composer-require.sh --no-release-info         # skip release information
#   ./generate-composer-require.sh --run --release-detail    # execute and show full changelog
#
# Packages listed in the "ignore" section of generate-composer-require.yaml or .yml (or .ignore.txt for backward compatibility) will be skipped.
# Packages listed in the "include" section will be forced to be included, even if they are in the ignore list.
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

# Emoji variables (defined once for performance)
E_OK="✅"
E_WRENCH="🔧"
E_CLIPBOARD="📋"
E_PACKAGE="📦"
E_LINK="🔗"
E_MEMO="📝"
E_ROCKET="🚀"
E_ERROR="❌"
E_SKIP="⏭️"

# Show help function
show_help() {
    cat <<EOF
Usage: $0 [OPTIONS]

Generates composer require commands from "composer outdated --direct".
Works with any PHP project (Symfony, Laravel, Yii, CodeIgniter, CakePHP, Laminas, Slim, etc.)

OPTIONS:
    --run                    Execute the suggested commands automatically
    --release-info           Show release information (summary with links)
    --release-detail         Show full release changelog for each package
    --no-release-info        Skip release information section (default)
    -v, --verbose            Show verbose output (configuration files, packages, etc.)
    --debug                  Show debug information (very detailed, includes file paths, parsing, etc.)
    -h, --help               Show this help message

EXAMPLES:
    $0                                    # Show suggested commands (no release info)
    $0 --run                              # Execute suggested commands
    $0 --release-info                     # Show release information summary
    $0 --release-detail                    # Show full changelogs
    $0 --run --release-detail             # Execute and show full changelogs
    $0 --verbose                           # Show verbose output with configuration details
    $0 --debug                             # Show detailed debug information

FRAMEWORK SUPPORT:
    The script automatically respects framework version constraints:
    - Symfony: respects "extra.symfony.require" constraint
    - Laravel: respects laravel/framework major.minor version
    - Yii: respects yiisoft/yii2 major.minor version
    - CakePHP: respects cakephp/cakephp major.minor version
    - Laminas: respects laminas/* major.minor versions
    - CodeIgniter: respects codeigniter4/framework major.minor version
    - Slim: respects slim/slim major.minor version

IGNORED PACKAGES:
    Packages listed in generate-composer-require.yaml or .yml will be skipped.
    The script searches for configuration files in the current directory (where composer.json is located).
    Old format (generate-composer-require.ignore.txt) is still supported for backward compatibility.
    Comments starting with # are ignored.

RELEASE INFORMATION:
    By default, release information is NOT shown (no API calls are made).

    Use --release-info to show a summary with:
    - Package name
    - Release URL
    - Changelog URL

    Use --release-detail to see the full release changelog.
    Use --no-release-info to explicitly skip release information (default behavior).

EOF
}

# Parse command line arguments
RUN_FLAG=""
SHOW_RELEASE_DETAIL=false
SHOW_RELEASE_INFO=false
VERBOSE=false
DEBUG=false

for arg in "$@"; do
    case "$arg" in
        -h|--help)
            show_help
            exit 0
            ;;
        --run)
            RUN_FLAG="--run"
            ;;
        --release-info|--releases)
            SHOW_RELEASE_INFO=true
            ;;
        --release-detail|--release-full|--detail)
            SHOW_RELEASE_INFO=true
            SHOW_RELEASE_DETAIL=true
            ;;
        --no-release-info|--skip-releases|--no-releases)
            SHOW_RELEASE_INFO=false
            ;;
        -v|--verbose)
            VERBOSE=true
            ;;
        --debug)
            DEBUG=true
            VERBOSE=true
            ;;
        *)
            echo "$E_ERROR  Unknown option: $arg" >&2
            echo "" >&2
            echo "Use --help or -h for usage information." >&2
            exit 1
            ;;
    esac
done

# Binaries (check after processing --help)
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="$(command -v composer || true)"

if [ -z "$COMPOSER_BIN" ]; then
  echo "$E_ERROR  Composer is not installed or not in PATH." >&2
  exit 1
fi

if [ ! -f composer.json ]; then
  echo "$E_ERROR  composer.json not found in the current directory." >&2
  exit 1
fi

# Load ignored and included packages from YAML file (if exists)
# Fallback to old TXT file for backward compatibility
# Search in current directory (where composer.json is), not in script directory
# Support both .yaml and .yml extensions (priority: .yaml first)
IGNORE_FILE_YAML=""
IGNORE_FILE_YML=""
IGNORE_FILE_TXT="generate-composer-require.ignore.txt"
IGNORED_PACKAGES=""
INCLUDED_PACKAGES=""

# Debug: Show current directory and files being searched
if [ "$DEBUG" = "true" ]; then
  echo "🔍 DEBUG: Current directory: $(pwd)" >&2
  echo "🔍 DEBUG: Searching for configuration files:" >&2
  echo "   - generate-composer-require.yaml" >&2
  echo "   - generate-composer-require.yml" >&2
  echo "   - $IGNORE_FILE_TXT" >&2
fi

# Check for .yaml first, then .yml, then .txt
if [ -f "generate-composer-require.yaml" ]; then
  IGNORE_FILE_YAML="generate-composer-require.yaml"
  if [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ]; then
    echo "📋 Found configuration file: generate-composer-require.yaml" >&2
  fi
elif [ -f "generate-composer-require.yml" ]; then
  IGNORE_FILE_YML="generate-composer-require.yml"
  if [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ]; then
    echo "📋 Found configuration file: generate-composer-require.yml" >&2
  fi
elif [ -f "$IGNORE_FILE_TXT" ]; then
  # Fallback to old TXT format for backward compatibility
  if [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ]; then
    echo "📋 Found configuration file: $IGNORE_FILE_TXT (old format)" >&2
  fi
  IGNORED_PACKAGES="$(grep -v '^\s*#' "$IGNORE_FILE_TXT" | grep -v '^\s*$' | tr '\n' '|' | sed 's/|$//' || true)"
  if [ "$DEBUG" = "true" ]; then
    echo "🔍 DEBUG: Ignored packages from TXT: ${IGNORED_PACKAGES:-none}" >&2
  fi
else
  if [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ]; then
    echo "ℹ️  No configuration file found (using defaults)" >&2
  fi
fi

# Process YAML file (either .yaml or .yml)
if [ -n "$IGNORE_FILE_YAML" ] || [ -n "$IGNORE_FILE_YML" ]; then
  # Use .yaml if available, otherwise .yml
  YAML_FILE="${IGNORE_FILE_YAML:-$IGNORE_FILE_YML}"

  if [ "$DEBUG" = "true" ]; then
    echo "🔍 DEBUG: Processing YAML file: $YAML_FILE" >&2
    echo "🔍 DEBUG: File exists: $([ -f "$YAML_FILE" ] && echo 'yes' || echo 'no')" >&2
    if [ -f "$YAML_FILE" ]; then
      echo "🔍 DEBUG: File size: $(wc -c < "$YAML_FILE") bytes" >&2
    fi
  fi

  # Read YAML file and extract packages from ignore array
  # Improved YAML parsing: extract lines starting with "  - " or "    - " after "ignore:"
  # Handles different indentation levels and inline comments
  IGNORED_PACKAGES="$(awk '
    /^ignore:/{flag=1; next}
    flag && /^[^ ]/{flag=0}
    flag && /^\s*-\s+([^#]+)/{
      gsub(/^\s*-\s+/, "");
      gsub(/\s*#.*$/, "");
      gsub(/^\s+|\s+$/, "");
      if ($0 != "") print
    }
  ' "$YAML_FILE" | tr '\n' '|' | sed 's/|$//' || true)"

  if [ "$DEBUG" = "true" ]; then
    echo "🔍 DEBUG: Ignored packages from YAML: ${IGNORED_PACKAGES:-none}" >&2
    if [ -n "$IGNORED_PACKAGES" ]; then
      echo "🔍 DEBUG: Ignored packages list:" >&2
      echo "$IGNORED_PACKAGES" | tr '|' '\n' | sed 's/^/   - /' >&2
    else
      echo "🔍 DEBUG: No ignored packages found (all packages are commented or section is empty)" >&2
    fi
  elif [ "$VERBOSE" = "true" ]; then
    if [ -n "$IGNORED_PACKAGES" ]; then
      echo "📋 Ignored packages: $(echo "$IGNORED_PACKAGES" | tr '|' ', ')" >&2
    else
      echo "📋 Ignored packages: none (all packages are commented or section is empty)" >&2
    fi
  fi

  # Read YAML file and extract packages from include array (if exists)
  # Improved YAML parsing: extract lines starting with "  - " or "    - " after "include:"
  # Handles different indentation levels and inline comments
  INCLUDED_PACKAGES="$(awk '
    /^include:/{flag=1; next}
    flag && /^[^ ]/{flag=0}
    flag && /^\s*-\s+([^#]+)/{
      gsub(/^\s*-\s+/, "");
      gsub(/\s*#.*$/, "");
      gsub(/^\s+|\s+$/, "");
      if ($0 != "") print
    }
  ' "$YAML_FILE" | tr '\n' '|' | sed 's/|$//' || true)"

  if [ "$DEBUG" = "true" ]; then
    echo "🔍 DEBUG: Included packages from YAML: ${INCLUDED_PACKAGES:-none}" >&2
    if [ -n "$INCLUDED_PACKAGES" ]; then
      echo "🔍 DEBUG: Included packages list:" >&2
      echo "$INCLUDED_PACKAGES" | tr '|' '\n' | sed 's/^/   - /' >&2
    else
      echo "🔍 DEBUG: No included packages found (all packages are commented or section is empty)" >&2
    fi
  elif [ "$VERBOSE" = "true" ]; then
    if [ -n "$INCLUDED_PACKAGES" ]; then
      echo "📋 Included packages: $(echo "$INCLUDED_PACKAGES" | tr '|' ', ')" >&2
    else
      echo "📋 Included packages: none (all packages are commented or section is empty)" >&2
    fi
  fi
fi

# Run composer with forced timezone to avoid warnings
# and filter any line starting with "Warning:" just in case.
# Show loading indicator while checking outdated packages
if [ "$DEBUG" != "true" ]; then
  echo -n "⏳ Checking for outdated packages... " >&2
fi
OUTDATED_JSON="$("$PHP_BIN" -d date.timezone=UTC "$COMPOSER_BIN" outdated --direct --format=json 2>&1 \
  | grep -v '^Warning:' || true)"
if [ "$DEBUG" != "true" ]; then
  echo "✅" >&2
fi

if [ "$DEBUG" = "true" ]; then
  echo "🔍 DEBUG: Composer outdated command executed" >&2
  echo "🔍 DEBUG: OUTDATED_JSON length: ${#OUTDATED_JSON} characters" >&2
fi

if [ -z "${OUTDATED_JSON}" ]; then
  if [ "$DEBUG" = "true" ]; then
    echo "🔍 DEBUG: Composer outdated returned empty JSON" >&2
  fi
  echo "$E_OK  No outdated direct dependencies."
  exit 0
fi

# Process JSON with PHP (also with forced timezone)
if [ "$DEBUG" = "true" ]; then
  echo "🔍 DEBUG: Passing to PHP script:" >&2
  echo "   - IGNORED_PACKAGES: ${IGNORED_PACKAGES:-none}" >&2
  echo "   - INCLUDED_PACKAGES: ${INCLUDED_PACKAGES:-none}" >&2
  echo "   - SHOW_RELEASE_INFO: $SHOW_RELEASE_INFO" >&2
elif [ "$VERBOSE" != "true" ]; then
  echo -n "⏳ Processing packages... " >&2
fi

OUTPUT="$(OUTDATED_JSON="$OUTDATED_JSON" COMPOSER_BIN="$COMPOSER_BIN" PHP_BIN="$PHP_BIN" IGNORED_PACKAGES="$IGNORED_PACKAGES" INCLUDED_PACKAGES="$INCLUDED_PACKAGES" SHOW_RELEASE_INFO="$SHOW_RELEASE_INFO" DEBUG="$DEBUG" "$PHP_BIN" -d date.timezone=UTC <<'PHP'
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

// Load included packages from environment (force include even if ignored)
$includedPackagesRaw = getenv('INCLUDED_PACKAGES') ?: '';
$includedPackages = [];
if ($includedPackagesRaw) {
    $includedPackages = array_flip(explode('|', $includedPackagesRaw));
}

// Check if release info should be skipped
$showReleaseInfo = getenv('SHOW_RELEASE_INFO') !== 'false';
$debug = getenv('DEBUG') === 'true';

if ($debug) {
    error_log("DEBUG: showReleaseInfo = " . ($showReleaseInfo ? 'true' : 'false'));
    error_log("DEBUG: ignoredPackages count = " . count($ignoredPackages));
    error_log("DEBUG: includedPackages count = " . count($includedPackages));
    if (count($ignoredPackages) > 0) {
        error_log("DEBUG: ignoredPackages list: " . implode(', ', array_keys($ignoredPackages)));
    }
    if (count($includedPackages) > 0) {
        error_log("DEBUG: includedPackages list: " . implode(', ', array_keys($includedPackages)));
    }
    error_log("DEBUG: Total outdated packages: " . count($report['installed']));
    error_log("DEBUG: require packages: " . count($require));
    error_log("DEBUG: require-dev packages: " . count($requireDev));
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
        if ($debug) {
            error_log("DEBUG: Detected Symfony constraint: {$baseVersion}.* (from extra.symfony.require)");
        }
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
            if ($debug) {
                error_log("DEBUG: Detected {$name} framework constraint: {$baseVersion}.* (from {$corePackage})");
            }
            // Also add related prefixes (e.g., illuminate/ for Laravel)
            if (isset($config['related'])) {
                foreach ($config['related'] as $relatedPrefix) {
                    $frameworkConstraints[$relatedPrefix] = $baseVersion;
                    if ($debug) {
                        error_log("DEBUG: Added related prefix constraint: {$relatedPrefix} = {$baseVersion}.*");
                    }
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

// Function to get GitHub repository URL from Packagist
function getGitHubRepoFromPackagist($packageName) {
    $url = "https://packagist.org/packages/{$packageName}.json";
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Composer Update Helper',
        ]
    ]);

    $json = @file_get_contents($url, false, $context);
    if (!$json) {
        return null;
    }

    $data = json_decode($json, true);
    if (!$data || !isset($data['package']['repository'])) {
        return null;
    }

    $repoUrl = $data['package']['repository'];
    // Extract GitHub repo from URL (e.g., https://github.com/user/repo.git -> user/repo)
    if (preg_match('#github\.com[:/]([^/]+/[^/]+?)(?:\.git)?/?$#', $repoUrl, $matches)) {
        return $matches[1];
    }

    return null;
}

// Function to get release information from GitHub
function getReleaseInfo($githubRepo, $version) {
    if (!$githubRepo) {
        return null;
    }

    // Normalize version (remove 'v' prefix if present)
    $normalizedVersion = ltrim($version, 'v');

    // Try to get release by tag
    $url = "https://api.github.com/repos/{$githubRepo}/releases/tags/v{$normalizedVersion}";
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Composer Update Helper',
            'header' => 'Accept: application/vnd.github.v3+json',
        ]
    ]);

    $json = @file_get_contents($url, false, $context);
    if ($json) {
        $release = json_decode($json, true);
        if ($release && isset($release['html_url'])) {
            return [
                'url' => $release['html_url'],
                'name' => $release['name'] ?? $release['tag_name'] ?? $version,
                'body' => $release['body'] ?? '',
                'published_at' => $release['published_at'] ?? null,
            ];
        }
    }

    // If not found, try without 'v' prefix
    $url = "https://api.github.com/repos/{$githubRepo}/releases/tags/{$normalizedVersion}";
    $json = @file_get_contents($url, false, $context);
    if ($json) {
        $release = json_decode($json, true);
        if ($release && isset($release['html_url'])) {
            return [
                'url' => $release['html_url'],
                'name' => $release['name'] ?? $release['tag_name'] ?? $version,
                'body' => $release['body'] ?? '',
                'published_at' => $release['published_at'] ?? null,
            ];
        }
    }

    // Try latest release if exact version not found
    $url = "https://api.github.com/repos/{$githubRepo}/releases/latest";
    $json = @file_get_contents($url, false, $context);
    if ($json) {
        $release = json_decode($json, true);
        if ($release && isset($release['html_url'])) {
            $latestVersion = ltrim($release['tag_name'] ?? '', 'v');
            if ($latestVersion === $normalizedVersion) {
                return [
                    'url' => $release['html_url'],
                    'name' => $release['name'] ?? $release['tag_name'] ?? $version,
                    'body' => $release['body'] ?? '',
                    'published_at' => $release['published_at'] ?? null,
                ];
            }
        }
    }

    return null;
}

// ============================================================================
// PROCESS PACKAGES
// ============================================================================

$prod = [];
$dev  = [];
$ignoredProd = [];
$ignoredDev  = [];
$releaseInfo = []; // Store release information for packages

foreach ($report['installed'] as $pkg) {
    if (!isset($pkg['name'])) continue;
    $name   = $pkg['name'];
    $installed = $pkg['version'] ?? null;
    $latest = $pkg['latest'] ?? null;

    if ($debug) {
        error_log("DEBUG: Processing package: {$name} (installed: {$installed}, latest: {$latest})");
    }

    // Check if package is included (force include even if ignored)
    $isIncluded = isset($includedPackages[$name]);
    $isIgnored = isset($ignoredPackages[$name]);

    if ($debug) {
        error_log("DEBUG:   - isIgnored: " . ($isIgnored ? 'true' : 'false'));
        error_log("DEBUG:   - isIncluded: " . ($isIncluded ? 'true' : 'false'));
    }

    // Check if package is ignored (unless it's explicitly included)
    if ($isIgnored && !$isIncluded) {
        if ($debug) {
            error_log("DEBUG:   - Action: IGNORED (in ignore list and not in include list)");
        }
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

    if ($isIncluded && $debug) {
        error_log("DEBUG:   - Action: INCLUDED (forced include, overriding ignore)");
    }

    if (!$latest) {
        if ($debug) {
            error_log("DEBUG:   - Action: SKIPPED (no latest version available)");
        }
        continue;
    }

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
            $comparison = version_compare($installedNormalized, $constraintNormalized, '>=');
            if ($debug) {
                error_log("DEBUG:   - Version comparison: {$installedNormalized} >= {$constraintNormalized} = " . ($comparison ? 'true' : 'false'));
            }
            if ($comparison) {
                // Already at that version or higher, don't include
                if ($debug) {
                    error_log("DEBUG:   - Action: SKIPPED (already at or above target version)");
                }
                continue;
            }
        } elseif ($debug) {
            error_log("DEBUG:   - Wildcard constraint, including for update");
        }
    }

          // Get release information for this package (only for specific versions, not wildcards)
          if ($showReleaseInfo && strpos($constraint, '*') === false && strpos($constraint, '^') === false && strpos($constraint, '~') === false) {
              // Only fetch release info for specific versions to avoid unnecessary API calls
              // Show progress indicator (only if not in debug mode, as debug shows everything)
              if (!$debug && !isset($releaseInfoShown)) {
                  error_log("⏳ Fetching release information...");
                  $releaseInfoShown = true;
              }
              $githubRepo = getGitHubRepoFromPackagist($name);
              if ($githubRepo) {
                  $release = getReleaseInfo($githubRepo, $latest);
                  if ($release) {
                      $releaseInfo[$name] = $release;
                  }
              }
          }

    if (isset($devSet[$name])) {
        $dev[] = $name . ':' . $constraint;
        if ($debug) {
            error_log("DEBUG:   - Action: ADDED to dev dependencies: {$name}:{$constraint}");
        }
    } else {
        $prod[] = $name . ':' . $constraint;
        if ($debug) {
            error_log("DEBUG:   - Action: ADDED to prod dependencies: {$name}:{$constraint}");
        }
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

if ($debug) {
    error_log("DEBUG: Generated commands:");
    error_log("DEBUG:   - Prod packages: " . count($prod) . " (" . implode(', ', $prod) . ")");
    error_log("DEBUG:   - Dev packages: " . count($dev) . " (" . implode(', ', $dev) . ")");
    error_log("DEBUG:   - Ignored prod: " . count($ignoredProd) . " (" . implode(', ', $ignoredProd) . ")");
    error_log("DEBUG:   - Ignored dev: " . count($ignoredDev) . " (" . implode(', ', $ignoredDev) . ")");
}

if ($debug) {
    error_log("DEBUG: Generated commands:");
    error_log("DEBUG:   - Prod packages: " . count($prod) . " (" . implode(', ', $prod) . ")");
    error_log("DEBUG:   - Dev packages: " . count($dev) . " (" . implode(', ', $dev) . ")");
    error_log("DEBUG:   - Ignored prod: " . count($ignoredProd) . " (" . implode(', ', $ignoredProd) . ")");
    error_log("DEBUG:   - Ignored dev: " . count($ignoredDev) . " (" . implode(', ', $ignoredDev) . ")");
}

// Ignored packages section
$output[] = "---IGNORED_PROD---";
$output[] = implode(' ', $ignoredProd);
$output[] = "---IGNORED_DEV---";
$output[] = implode(' ', $ignoredDev);

// Release information section
$output[] = "---RELEASES---";
$releaseData = [];
foreach ($releaseInfo as $pkgName => $info) {
    $releaseData[] = $pkgName . '|' . $info['url'] . '|' . base64_encode($info['name']) . '|' . base64_encode($info['body']) . '|' . ($info['published_at'] ?? '');
}
if (!empty($releaseData)) {
    $output[] = implode(PHP_EOL, $releaseData);
}

echo implode(PHP_EOL, $output);
PHP
)"

# Filter any "Warning:" that might have slipped in from PHP (double safety)
OUTPUT="$(printf "%s\n" "$OUTPUT" | grep -v '^Warning:' || true)"

if [ "$DEBUG" = "true" ]; then
  echo "🔍 DEBUG: PHP script output length: ${#OUTPUT} characters" >&2
  echo "🔍 DEBUG: Parsing structured output..." >&2
elif [ "$VERBOSE" != "true" ]; then
  echo "✅" >&2
fi

# Show completion message if release info was fetched
if [ "$SHOW_RELEASE_INFO" = "true" ] && [ "$DEBUG" != "true" ]; then
  if printf "%s\n" "$OUTPUT" | grep -q "^---RELEASES---"; then
    releaseCount=$(printf "%s\n" "$OUTPUT" | sed -n '/^---RELEASES---$/,/^$/p' | grep -v '^---' | grep -v '^$' | wc -l)
    if [ "$releaseCount" -gt 0 ] 2>/dev/null; then
      echo "✅ Release information fetched for $releaseCount package(s)" >&2
    fi
  fi
fi

# Parse the structured output
FRAMEWORKS="$(printf "%s\n" "$OUTPUT" | sed -n '/^---FRAMEWORKS---$/,/^---COMMANDS---$/p' | grep -v '^---' || true)"
COMMANDS="$(printf "%s\n" "$OUTPUT" | sed -n '/^---COMMANDS---$/,/^---IGNORED_PROD---$/p' | grep -v '^---' || true)"
IGNORED_PROD="$(printf "%s\n" "$OUTPUT" | sed -n '/^---IGNORED_PROD---$/,/^---IGNORED_DEV---$/p' | grep -v '^---' || true)"
IGNORED_DEV="$(printf "%s\n" "$OUTPUT" | sed -n '/^---IGNORED_DEV---$/,/^---RELEASES---$/p' | grep -v '^---' || true)"
RELEASES="$(printf "%s\n" "$OUTPUT" | sed -n '/^---RELEASES---$/,$p' | grep -v '^---' || true)"

# Check if there's anything to show
if [ -z "$COMMANDS" ] && [ -z "$IGNORED_PROD" ] && [ -z "$IGNORED_DEV" ]; then
  if [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ]; then
    echo "ℹ️  No outdated direct dependencies found." >&2
  fi
  echo "$E_OK  No outdated direct dependencies."
  exit 0
fi

if [ "$DEBUG" = "true" ]; then
  echo "🔍 DEBUG: Parsed output:" >&2
  echo "   - FRAMEWORKS: ${FRAMEWORKS:-none}" >&2
  echo "   - COMMANDS: ${COMMANDS:-none}" >&2
  echo "   - IGNORED_PROD: ${IGNORED_PROD:-none}" >&2
  echo "   - IGNORED_DEV: ${IGNORED_DEV:-none}" >&2
  echo "   - RELEASES: ${RELEASES:-none}" >&2
fi

# Show detected frameworks
if [ -n "$FRAMEWORKS" ]; then
  echo "$E_WRENCH  Detected framework constraints:"
  printf "%s\n" "$FRAMEWORKS" | tr ' ' '\n' | while read -r fw; do
    [ -n "$fw" ] && echo "  - $fw"
  done
  echo ""
fi

# Show ignored packages if any (prod)
if [ -n "$IGNORED_PROD" ]; then
  echo "$E_SKIP   Ignored packages (prod):"
  printf "%s\n" "$IGNORED_PROD" | tr ' ' '\n' | while read -r pkg; do
    [ -n "$pkg" ] && echo "  - $pkg"
  done
  echo ""
fi

# Show ignored packages if any (dev)
if [ -n "$IGNORED_DEV" ]; then
  echo "$E_SKIP   Ignored packages (dev):"
  printf "%s\n" "$IGNORED_DEV" | tr ' ' '\n' | while read -r pkg; do
    [ -n "$pkg" ] && echo "  - $pkg"
  done
  echo ""
fi

if [ -z "$COMMANDS" ]; then
  echo "$E_OK  No packages to update (all outdated packages are ignored)."
  exit 0
fi

echo "$E_WRENCH  Suggested commands:"
printf "%s\n" "$COMMANDS" | sed 's/^/  /'

# Show release information if available
if [ -n "$RELEASES" ] && [ "$SHOW_RELEASE_INFO" = "true" ]; then
  echo ""
  echo "$E_CLIPBOARD  Release information:"
  printf "%s\n" "$RELEASES" | while IFS= read -r line; do
    [ -z "$line" ] && continue
    # Parse the line: pkgName|url|nameB64|bodyB64|publishedAt
    pkgName=$(printf "%s" "$line" | cut -d'|' -f1)
    releaseUrl=$(printf "%s" "$line" | cut -d'|' -f2)
    releaseNameB64=$(printf "%s" "$line" | cut -d'|' -f3)
    releaseBodyB64=$(printf "%s" "$line" | cut -d'|' -f4)

    [ -z "$pkgName" ] && continue

    releaseName=""
    if [ -n "$releaseNameB64" ]; then
      releaseName=$(printf "%s" "$releaseNameB64" | base64 -d 2>/dev/null || echo "")
    fi

    releaseBody=""
    if [ -n "$releaseBodyB64" ]; then
      releaseBody=$(printf "%s" "$releaseBodyB64" | base64 -d 2>/dev/null || echo "")
    fi

    # Extract changelog link (GitHub releases page)
    changelogUrl=""
    if [ -n "$releaseUrl" ]; then
      # Convert release tag URL to releases page URL
      changelogUrl=$(printf "%s" "$releaseUrl" | sed 's|/releases/tag/|/releases|')
    fi

    echo "  $E_PACKAGE  $pkgName"
    if [ -n "$releaseUrl" ]; then
      echo "     $E_LINK  Release: $releaseUrl"
    fi
    if [ -n "$changelogUrl" ] && [ "$changelogUrl" != "$releaseUrl" ]; then
      echo "     $E_MEMO  Changelog: $changelogUrl"
    fi

    # Show detailed information if --release-detail flag is set
    if [ "$SHOW_RELEASE_DETAIL" = "true" ]; then
      if [ -n "$releaseName" ] && [ "$releaseName" != "$pkgName" ] && [ "$releaseName" != "" ]; then
        echo "     $E_CLIPBOARD  $releaseName"
      fi
      if [ -n "$releaseBody" ]; then
        echo "     ──────────────────────────────────────"
        printf "%s" "$releaseBody" | sed 's/^/     /'
        echo ""
        echo "     ──────────────────────────────────────"
      fi
    fi
    echo ""
  done
fi

if [ "$RUN_FLAG" = "--run" ]; then
  echo ""
  echo "$E_ROCKET  Running..."
  printf "%s\n" "$COMMANDS" | while IFS= read -r cmd; do
    [ -z "$cmd" ] && continue
    echo "→ $cmd"
    # Run each command with forced timezone to avoid warnings during installation
    sh -lc "$PHP_BIN -d date.timezone=UTC $COMPOSER_BIN $(printf '%s' "$cmd" | sed 's/^composer //')"
  done
  echo "$E_OK  Update completed."
fi
