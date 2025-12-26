#!/bin/sh
# generate-composer-require.sh
# Lightweight wrapper script that delegates complex logic to PHP in vendor.
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
E_INFO="ℹ️"

# Show help function
show_help() {
    cat <<EOF
Usage: $0 [OPTIONS]

Generates composer require commands from "composer outdated --direct".
Works with any PHP project (Symfony, Laravel, Yii, CodeIgniter, CakePHP, Laminas, Slim, etc.)

OPTIONS:
    --run                    Execute the suggested commands automatically
    --release-info           Show release information (summary with links)
    --release-detail         Show full release changelog for each package (implies --release-info)
    --no-release-info        Skip release information section (default behavior)
    -v, --verbose            Show verbose output (configuration files, packages, etc.)
    --debug                  Show debug information (very detailed, includes file paths, parsing, etc.)
    -h, --help               Show this help message

EXAMPLES:
    $0                                    # Show suggested commands (no release info by default)
    $0 --run                              # Execute suggested commands
    $0 --release-info                     # Show release information summary
    $0 --release-detail                   # Show full changelogs (implies --release-info)
    $0 --verbose                           # Show verbose output with configuration details
    $0 --debug                             # Show detailed debug information
    $0 --run --release-detail             # Execute and show full changelogs
    $0 --verbose --release-info # Verbose with release info
    $0 --debug                  # Debug mode (very detailed)

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
        -v|--verbose)
            VERBOSE=true
            ;;
        --debug)
            DEBUG=true
            VERBOSE=true # Debug implies verbose
            ;;
        --no-release-info|--skip-releases|--no-releases)
            SHOW_RELEASE_INFO=false
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

# Find the PHP processor script in vendor
# Try vendor first, then fallback to script directory (for development)
PROCESSOR_PHP=""
if [ -f "vendor/nowo-tech/composer-update-helper/bin/process-updates.php" ]; then
  PROCESSOR_PHP="vendor/nowo-tech/composer-update-helper/bin/process-updates.php"
elif [ -f "$(dirname "$0")/process-updates.php" ]; then
  PROCESSOR_PHP="$(dirname "$0")/process-updates.php"
else
  echo "$E_ERROR  Could not find process-updates.php in vendor or script directory." >&2
  echo "   Please run: composer install" >&2
  exit 1
fi

if [ "$DEBUG" = "true" ]; then
  echo "🔍 DEBUG: Processor PHP found at: $PROCESSOR_PHP" >&2
fi

# Detect configuration file (YAML parsing is now done in PHP)
# Search in current directory (where composer.json is), not in script directory
# Support both .yaml and .yml extensions (priority: .yaml first)
CONFIG_FILE=""

# Debug: Show current directory and files being searched
if [ "$DEBUG" = "true" ]; then
  echo "🔍 DEBUG: Current directory: $(pwd)" >&2
  echo "🔍 DEBUG: Searching for configuration files:" >&2
  echo "   - generate-composer-require.yaml" >&2
  echo "   - generate-composer-require.yml" >&2
  echo "   - generate-composer-require.ignore.txt" >&2
fi

# Check for .yaml first, then .yml, then .txt
if [ -f "generate-composer-require.yaml" ]; then
  CONFIG_FILE="generate-composer-require.yaml"
  if [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ]; then
    echo "📋 Found configuration file: generate-composer-require.yaml" >&2
  fi
elif [ -f "generate-composer-require.yml" ]; then
  CONFIG_FILE="generate-composer-require.yml"
  if [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ]; then
    echo "📋 Found configuration file: generate-composer-require.yml" >&2
  fi
elif [ -f "generate-composer-require.ignore.txt" ]; then
  # Fallback to old TXT format for backward compatibility
  CONFIG_FILE="generate-composer-require.ignore.txt"
  if [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ]; then
    echo "📋 Found configuration file: generate-composer-require.ignore.txt (old format)" >&2
  fi
else
  if [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ]; then
    echo "ℹ️  No configuration file found (using defaults)" >&2
  fi
fi

# YAML parsing is now done in PHP (process-updates.php)
# We just pass the config file path as an environment variable

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
# YAML parsing is now done in PHP, we just pass the config file path
if [ "$DEBUG" = "true" ]; then
  echo "🔍 DEBUG: Passing to PHP script:" >&2
  echo "   - CONFIG_FILE: ${CONFIG_FILE:-none}" >&2
  echo "   - SHOW_RELEASE_INFO: $SHOW_RELEASE_INFO" >&2
  echo "   - DEBUG: $DEBUG" >&2
  echo "   - PROCESSOR_PHP: $PROCESSOR_PHP" >&2
elif [ "$VERBOSE" != "true" ]; then
  echo -n "⏳ Processing packages... " >&2
fi

# Call the PHP processor script
# PHP will read the YAML/TXT file directly from CONFIG_FILE
OUTPUT="$(OUTDATED_JSON="$OUTDATED_JSON" COMPOSER_BIN="$COMPOSER_BIN" PHP_BIN="$PHP_BIN" CONFIG_FILE="$CONFIG_FILE" SHOW_RELEASE_INFO="$SHOW_RELEASE_INFO" DEBUG="$DEBUG" VERBOSE="$VERBOSE" "$PHP_BIN" -d date.timezone=UTC "$PROCESSOR_PHP" 2>&1)"

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
