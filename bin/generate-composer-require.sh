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
E_LOADING="⏳"
E_DEBUG="🔍"
E_CHECK="✅"

# Message variables (to avoid repetition)
MSG_LOADING_CONFIG="Loading configuration... "
MSG_CHECKING_OUTDATED="Checking for outdated packages... "
MSG_PROCESSING="Processing packages... "
MSG_PROCESSING_PHP="Processing packages with PHP script... "
MSG_RUNNING="Running..."
MSG_UPDATE_COMPLETED="Update completed."
MSG_NO_OUTDATED="No outdated direct dependencies."
MSG_DEBUG_PREFIX="DEBUG: "
MSG_FOUND_CONFIG="Found configuration file: "
MSG_NO_CONFIG="No configuration file found (using defaults)"
MSG_COMPOSER_NOT_FOUND="Composer is not installed or not in PATH."
MSG_COMPOSER_JSON_NOT_FOUND="composer.json not found in the current directory."
MSG_PROCESSOR_NOT_FOUND="Could not find process-updates.php in vendor or script directory."
MSG_PLEASE_INSTALL="Please run: composer install"
MSG_UNKNOWN_OPTION="Unknown option:"
MSG_USE_HELP="Use --help or -h for usage information."
MSG_DEBUG_CURRENT_DIR="Current directory:"
MSG_DEBUG_SEARCHING_CONFIG="Searching for configuration files:"
MSG_DEBUG_COMPOSER_EXECUTED="Composer outdated command executed"
MSG_DEBUG_JSON_LENGTH="OUTDATED_JSON length:"
MSG_DEBUG_EMPTY_JSON="Composer outdated returned empty JSON"
MSG_DEBUG_PASSING_TO_PHP="Passing to PHP script:"
MSG_DEBUG_OUTPUT_LENGTH="PHP script output length:"
MSG_DEBUG_PROCESSOR_FOUND="Processor PHP found at:"

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
            echo "$E_ERROR  $MSG_UNKNOWN_OPTION $arg" >&2
            echo "" >&2
            echo "$MSG_USE_HELP" >&2
            exit 1
            ;;
    esac
done

# Binaries (check after processing --help)
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="$(command -v composer || true)"

if [ -z "$COMPOSER_BIN" ]; then
  echo "$E_ERROR  $MSG_COMPOSER_NOT_FOUND" >&2
  exit 1
fi

if [ ! -f composer.json ]; then
  echo "$E_ERROR  $MSG_COMPOSER_JSON_NOT_FOUND" >&2
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
  echo "$E_ERROR  $MSG_PROCESSOR_NOT_FOUND" >&2
  echo "   $MSG_PLEASE_INSTALL" >&2
  exit 1
fi

if [ "$DEBUG" = "true" ]; then
  echo "$E_DEBUG $MSG_DEBUG_PREFIX$MSG_DEBUG_PROCESSOR_FOUND $PROCESSOR_PHP" >&2
fi

# Detect configuration file (YAML parsing is now done in PHP)
# Search in current directory (where composer.json is), not in script directory
# Support both .yaml and .yml extensions (priority: .yaml first)
CONFIG_FILE=""

# Debug: Show current directory and files being searched
if [ "$DEBUG" = "true" ]; then
  echo "$E_DEBUG $MSG_DEBUG_PREFIX$MSG_DEBUG_CURRENT_DIR $(pwd)" >&2
  echo "$E_DEBUG $MSG_DEBUG_PREFIX$MSG_DEBUG_SEARCHING_CONFIG" >&2
  echo "   - generate-composer-require.yaml" >&2
  echo "   - generate-composer-require.yml" >&2
  echo "   - generate-composer-require.ignore.txt" >&2
fi

# Check for .yaml first, then .yml, then .txt
if [ -f "generate-composer-require.yaml" ]; then
  CONFIG_FILE="generate-composer-require.yaml"
  CONFIG_FILE_DISPLAY="generate-composer-require.yaml"
  CONFIG_FILE_SUFFIX=""
elif [ -f "generate-composer-require.yml" ]; then
  CONFIG_FILE="generate-composer-require.yml"
  CONFIG_FILE_DISPLAY="generate-composer-require.yml"
  CONFIG_FILE_SUFFIX=""
elif [ -f "generate-composer-require.ignore.txt" ]; then
  # Fallback to old TXT format for backward compatibility
  CONFIG_FILE="generate-composer-require.ignore.txt"
  CONFIG_FILE_DISPLAY="generate-composer-require.ignore.txt"
  CONFIG_FILE_SUFFIX=" (old format)"
else
  CONFIG_FILE=""
  CONFIG_FILE_DISPLAY=""
  CONFIG_FILE_SUFFIX=""
fi

# Display configuration file info
if [ -n "$CONFIG_FILE" ]; then
  if [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ]; then
    echo "$E_CLIPBOARD $MSG_FOUND_CONFIG$CONFIG_FILE_DISPLAY$CONFIG_FILE_SUFFIX" >&2
  fi
  if [ "$DEBUG" != "true" ] && [ "$VERBOSE" != "true" ]; then
    printf "$E_LOADING$MSG_LOADING_CONFIG$E_CHECK\n" >&2
  elif [ "$DEBUG" = "true" ]; then
    echo "$E_LOADING$MSG_LOADING_CONFIG" >&2
  fi
else
  if [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ]; then
    echo "$E_INFO  $MSG_NO_CONFIG" >&2
  fi
fi

# YAML parsing is now done in PHP (process-updates.php)
# We just pass the config file path as an environment variable

# Function to show animated spinner while command runs
show_spinner() {
  local pid=$1
  local message=$2
  local spinstr='|/-\'
  local temp

  if [ "$DEBUG" = "true" ] || [ "$VERBOSE" = "true" ]; then
    wait $pid
    return $?
  fi

  echo -n "$message" >&2
  while kill -0 $pid 2>/dev/null; do
    temp=${spinstr#?}
    printf "\b${spinstr:0:1}" >&2
    spinstr=$temp${spinstr%"$temp"}
    sleep 0.1
  done
  printf "\b$E_CHECK\n" >&2
  wait $pid
  return $?
}

# Run composer with forced timezone to avoid warnings
# and filter any line starting with "Warning:" just in case.
# Show loading indicator while checking outdated packages
# Run command in background and show spinner
("$PHP_BIN" -d date.timezone=UTC "$COMPOSER_BIN" outdated --direct --format=json 2>&1 \
  | grep -v '^Warning:' || true) > /tmp/composer-outdated-$$.json &
OUTDATED_PID=$!

if [ "$DEBUG" != "true" ] && [ "$VERBOSE" != "true" ]; then
  show_spinner $OUTDATED_PID "$E_LOADING $MSG_CHECKING_OUTDATED"
elif [ "$DEBUG" = "true" ]; then
  echo "$E_LOADING $MSG_CHECKING_OUTDATED" >&2
  wait $OUTDATED_PID
else
  echo "$E_LOADING $MSG_CHECKING_OUTDATED" >&2
  wait $OUTDATED_PID
  echo "$E_CHECK" >&2
fi

OUTDATED_JSON="$(cat /tmp/composer-outdated-$$.json 2>/dev/null || true)"
rm -f /tmp/composer-outdated-$$.json

if [ "$DEBUG" = "true" ]; then
  echo "$E_DEBUG $MSG_DEBUG_PREFIX$MSG_DEBUG_COMPOSER_EXECUTED" >&2
  echo "$E_DEBUG $MSG_DEBUG_PREFIX$MSG_DEBUG_JSON_LENGTH ${#OUTDATED_JSON} characters" >&2
fi

if [ -z "${OUTDATED_JSON}" ]; then
  if [ "$DEBUG" = "true" ]; then
    echo "$E_DEBUG $MSG_DEBUG_PREFIX$MSG_DEBUG_EMPTY_JSON" >&2
  fi
  echo "$E_OK  $MSG_NO_OUTDATED"
  exit 0
fi

# Process JSON with PHP (also with forced timezone)
# YAML parsing is now done in PHP, we just pass the config file path
if [ "$DEBUG" = "true" ]; then
  echo "$E_DEBUG $MSG_DEBUG_PREFIX$MSG_DEBUG_PASSING_TO_PHP" >&2
  echo "   - CONFIG_FILE: ${CONFIG_FILE:-none}" >&2
  echo "   - SHOW_RELEASE_INFO: $SHOW_RELEASE_INFO" >&2
  echo "   - DEBUG: $DEBUG" >&2
  echo "   - PROCESSOR_PHP: $PROCESSOR_PHP" >&2
fi

# Call the PHP processor script in background and show spinner
# PHP will read the YAML/TXT file directly from CONFIG_FILE
(OUTDATED_JSON="$OUTDATED_JSON" COMPOSER_BIN="$COMPOSER_BIN" PHP_BIN="$PHP_BIN" CONFIG_FILE="$CONFIG_FILE" SHOW_RELEASE_INFO="$SHOW_RELEASE_INFO" DEBUG="$DEBUG" VERBOSE="$VERBOSE" "$PHP_BIN" -d date.timezone=UTC "$PROCESSOR_PHP" 2>&1 \
  | grep -v '^Warning:' || true) > /tmp/composer-process-$$.out &
PROCESS_PID=$!

if [ "$DEBUG" != "true" ] && [ "$VERBOSE" != "true" ]; then
  show_spinner $PROCESS_PID "$E_LOADING $MSG_PROCESSING"
elif [ "$DEBUG" = "true" ]; then
  echo "$E_LOADING $MSG_PROCESSING_PHP" >&2
  wait $PROCESS_PID
else
  echo "$E_LOADING $MSG_PROCESSING" >&2
  wait $PROCESS_PID
  echo "$E_CHECK" >&2
fi

OUTPUT="$(cat /tmp/composer-process-$$.out 2>/dev/null || true)"
rm -f /tmp/composer-process-$$.out

# Filter any "Warning:" that might have slipped in from PHP (double safety)
OUTPUT="$(printf "%s\n" "$OUTPUT" | grep -v '^Warning:' || true)"

if [ "$DEBUG" = "true" ]; then
  echo "$E_DEBUG $MSG_DEBUG_PREFIX$MSG_DEBUG_OUTPUT_LENGTH ${#OUTPUT} characters" >&2
fi

# Extract commands for --run flag (between COMMANDS_START and COMMANDS_END markers)
COMMANDS=""
if printf "%s\n" "$OUTPUT" | grep -q "^---COMMANDS_START---"; then
  COMMANDS="$(printf "%s\n" "$OUTPUT" | sed -n '/^---COMMANDS_START---$/,/^---COMMANDS_END---$/p' | grep -v '^---' || true)"
fi

# Remove command markers and their content from output before displaying
OUTPUT="$(printf "%s\n" "$OUTPUT" | sed '/^---COMMANDS_START---$/,/^---COMMANDS_END---$/d' || true)"

# Display the formatted output from PHP
printf "%s\n" "$OUTPUT"

# Execute commands if --run flag is present
if [ "$RUN_FLAG" = "--run" ] && [ -n "$COMMANDS" ]; then
  echo ""
  echo "$E_ROCKET  $MSG_RUNNING"
  printf "%s\n" "$COMMANDS" | while IFS= read -r cmd; do
    [ -z "$cmd" ] && continue
    echo "→ $cmd"
    # Run each command with forced timezone to avoid warnings during installation
    sh -lc "$PHP_BIN -d date.timezone=UTC $COMPOSER_BIN $(printf '%s' "$cmd" | sed 's/^composer //')"
  done
  echo "$E_OK  $MSG_UPDATE_COMPLETED"
fi
