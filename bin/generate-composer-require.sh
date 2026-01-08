#!/bin/sh
# generate-composer-require.sh
# Lightweight wrapper script that delegates complex logic to PHP in vendor.
# Generates composer require commands (prod and dev) from "composer outdated --direct".
# Works with any PHP project (Symfony, Laravel, Yii, CodeIgniter, CakePHP, Laminas, Slim, etc.)

set -eu

# Find and load helper script from vendor
SCRIPT_DIR="$(dirname "$0")"
HELPER_PATHS="
vendor/nowo-tech/composer-update-helper/bin/script-helper.sh
${SCRIPT_DIR}/script-helper.sh
"
HELPER_LOADED=false
for helper_path in $HELPER_PATHS; do
    if [ -f "$helper_path" ]; then
        # shellcheck source=/dev/null
        . "$helper_path"
        HELPER_LOADED=true
        break
    fi
done

# Load i18n translation functions
I18N_LOADER_PATHS="
${SCRIPT_DIR}/i18n/translations-sh.sh
vendor/nowo-tech/composer-update-helper/bin/i18n/translations-sh.sh
"
I18N_LOADER_LOADED=false
for i18n_path in $I18N_LOADER_PATHS; do
    if [ -f "$i18n_path" ]; then
        # shellcheck source=/dev/null
        . "$i18n_path"
        I18N_LOADER_LOADED=true
        break
    fi
done

# Fallback functions if helpers not loaded
if [ "$HELPER_LOADED" = "false" ]; then
    detect_language_for_script() { echo "en"; }
    find_file_in_paths() {
        for path in $2; do [ -f "$path" ] && echo "$path" && break; done
    }
    detect_config_file() {
        [ -f "generate-composer-require.yaml" ] && echo "generate-composer-require.yaml|generate-composer-require.yaml|" && return
        [ -f "generate-composer-require.yml" ] && echo "generate-composer-require.yml|generate-composer-require.yml|" && return
        [ -f "generate-composer-require.ignore.txt" ] && echo "generate-composer-require.ignore.txt|generate-composer-require.ignore.txt| (old format)" && return
        echo "||"
    }
    show_loading() { wait "$1"; return $?; }
    get_help_file_path() { echo ""; }
fi

if [ "$I18N_LOADER_LOADED" = "false" ] && ! command -v t >/dev/null 2>&1; then
    t() { echo "$1"; }
    detect_language() { echo "en"; }
fi

# Simple message function (minimal fallback)
get_msg() {
    local key="$1"
    if command -v t >/dev/null 2>&1; then
        t "$key"
    else
        case "$key" in
            loading_config) echo "Loading configuration... " ;;
            checking_outdated) echo "Checking for outdated packages... " ;;
            processing) echo "Processing packages... " ;;
            processing_php) echo "Processing packages with PHP script... " ;;
            running) echo "Running..." ;;
            update_completed) echo "Update completed." ;;
            no_outdated) echo "No outdated direct dependencies." ;;
            debug_prefix) echo "DEBUG: " ;;
            found_config) echo "Found configuration file: " ;;
            no_config) echo "No configuration file found (using defaults)" ;;
            composer_not_found) echo "Composer is not installed or not in PATH." ;;
            composer_json_not_found) echo "composer.json not found in the current directory." ;;
            processor_not_found) echo "Could not find process-updates.php in vendor or script directory." ;;
            please_install) echo "Please run: composer install" ;;
            unknown_option) echo "Unknown option:" ;;
            use_help) echo "Use --help or -h for usage information." ;;
            debug_current_dir) echo "Current directory:" ;;
            debug_searching_config) echo "Searching for configuration files:" ;;
            debug_composer_executed) echo "Composer outdated command executed" ;;
            debug_json_length) echo "OUTDATED_JSON length:" ;;
            debug_empty_json) echo "Composer outdated returned empty JSON" ;;
            debug_passing_to_php) echo "Passing to PHP script:" ;;
            debug_output_length) echo "PHP script output length:" ;;
            debug_processor_found) echo "Processor PHP found at:" ;;
            *) echo "$key" ;;
        esac
    fi
}

# Emoji constants
E_OK="✅"; E_WRENCH="🔧"; E_CLIPBOARD="📋"; E_PACKAGE="📦"; E_LINK="🔗"
E_MEMO="📝"; E_ROCKET="🚀"; E_ERROR="❌"; E_SKIP="⏭️"; E_INFO="ℹ️"
E_LOADING="⏳"; E_DEBUG="🔍"; E_CHECK="✅"

# Show help function (delegates to vendor helper or shows minimal help)
show_help() {
    local help_lang=$(detect_language_for_script)
    local help_file=$(get_help_file_path "$help_lang" "$SCRIPT_DIR")

    # Try English fallback
    [ -z "$help_file" ] && help_file=$(get_help_file_path "en" "$SCRIPT_DIR")

    if [ -n "$help_file" ] && [ -f "$help_file" ]; then
        sed "s|%s|$0|g" "$help_file"
    else
        # Minimal fallback help (should rarely be needed)
        cat <<EOF
Usage: $0 [OPTIONS]

Generates composer require commands from "composer outdated --direct".

OPTIONS:
    --run, --release-info, --release-detail, --no-release-info
    -v, --verbose, --debug, -h, --help

Use --help for full documentation (requires vendor files).
EOF
    fi
}

# Parse arguments
RUN_FLAG=""; SHOW_RELEASE_DETAIL=false; SHOW_RELEASE_INFO=false; VERBOSE=false; DEBUG=false
for arg in "$@"; do
    case "$arg" in
        -h|--help) show_help; exit 0 ;;
        --run) RUN_FLAG="--run" ;;
        --release-info|--releases) SHOW_RELEASE_INFO=true ;;
        --release-detail|--release-full|--detail) SHOW_RELEASE_INFO=true; SHOW_RELEASE_DETAIL=true ;;
        -v|--verbose) VERBOSE=true ;;
        --debug) DEBUG=true; VERBOSE=true ;;
        --no-release-info|--skip-releases|--no-releases) SHOW_RELEASE_INFO=false ;;
        *) echo "❌  $(get_msg unknown_option) $arg" >&2; echo "" >&2; echo "$(get_msg use_help)" >&2; exit 1 ;;
    esac
done

# Check binaries
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="$(command -v composer || true)"
[ -z "$COMPOSER_BIN" ] && echo "❌  $(get_msg composer_not_found)" >&2 && exit 1
[ ! -f composer.json ] && echo "❌  $(get_msg composer_json_not_found)" >&2 && exit 1

# Find processor PHP (delegates to helper)
PROCESSOR_PATHS="
vendor/nowo-tech/composer-update-helper/bin/process-updates.php
${SCRIPT_DIR}/process-updates.php
"
PROCESSOR_PHP=$(find_file_in_paths "$PROCESSOR_PATHS")
[ -z "$PROCESSOR_PHP" ] && echo "❌  $(get_msg processor_not_found)" >&2 && echo "   $(get_msg please_install)" >&2 && exit 1

# Detect config file (delegates to helper)
config_info=$(detect_config_file)
CONFIG_FILE=$(echo "$config_info" | cut -d'|' -f1)
CONFIG_FILE_DISPLAY=$(echo "$config_info" | cut -d'|' -f2)
CONFIG_FILE_SUFFIX=$(echo "$config_info" | cut -d'|' -f3)

# Show config info
if [ -n "$CONFIG_FILE" ]; then
    [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ] && echo "📋 $(get_msg found_config)$CONFIG_FILE_DISPLAY$CONFIG_FILE_SUFFIX" >&2
    [ "$DEBUG" != "true" ] && [ "$VERBOSE" != "true" ] && printf "⏳ $(get_msg loading_config)✅\n" >&2
    [ "$DEBUG" = "true" ] && echo "⏳ $(get_msg loading_config)" >&2
else
    [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ] && echo "ℹ️  $(get_msg no_config)" >&2
fi

# Run composer outdated
("$PHP_BIN" -d date.timezone=UTC "$COMPOSER_BIN" outdated --direct --format=json 2>&1 | grep -v '^Warning:' || true) > /tmp/composer-outdated-$$.json &
OUTDATED_PID=$!

if [ "${DEBUG:-false}" = "true" ]; then
    echo "⏳ $(get_msg checking_outdated)" >&2
    show_loading $OUTDATED_PID ""
elif [ "${VERBOSE:-false}" = "true" ]; then
    echo "⏳ $(get_msg checking_outdated)" >&2
    show_loading $OUTDATED_PID ""
    echo "✅" >&2
else
    show_loading $OUTDATED_PID "⏳ $(get_msg checking_outdated)"
fi

OUTDATED_JSON="$(cat /tmp/composer-outdated-$$.json 2>/dev/null || true)"
rm -f /tmp/composer-outdated-$$.json

[ "$DEBUG" = "true" ] && echo "🔍 $(get_msg debug_prefix)$(get_msg debug_composer_executed)" >&2
[ "$DEBUG" = "true" ] && echo "🔍 $(get_msg debug_prefix)$(get_msg debug_json_length) ${#OUTDATED_JSON} characters" >&2

[ -z "${OUTDATED_JSON}" ] && [ "$DEBUG" = "true" ] && echo "🔍 $(get_msg debug_prefix)$(get_msg debug_empty_json)" >&2
[ -z "${OUTDATED_JSON}" ] && echo "✅  $(get_msg no_outdated)" && exit 0

# Process with PHP
[ "$DEBUG" = "true" ] && echo "🔍 $(get_msg debug_prefix)$(get_msg debug_passing_to_php)" >&2
[ "$DEBUG" = "true" ] && echo "   - CONFIG_FILE: ${CONFIG_FILE:-none}" >&2
[ "$DEBUG" = "true" ] && echo "   - SHOW_RELEASE_INFO: $SHOW_RELEASE_INFO" >&2
[ "$DEBUG" = "true" ] && echo "   - DEBUG: $DEBUG" >&2
[ "$DEBUG" = "true" ] && echo "   - PROCESSOR_PHP: $PROCESSOR_PHP" >&2

(OUTDATED_JSON="$OUTDATED_JSON" COMPOSER_BIN="$COMPOSER_BIN" PHP_BIN="$PHP_BIN" CONFIG_FILE="$CONFIG_FILE" SHOW_RELEASE_INFO="$SHOW_RELEASE_INFO" DEBUG="$DEBUG" VERBOSE="$VERBOSE" "$PHP_BIN" -d date.timezone=UTC "$PROCESSOR_PHP" 2>&1 | grep -v '^Warning:' || true) > /tmp/composer-process-$$.out &
PROCESS_PID=$!

if [ "${DEBUG:-false}" = "true" ]; then
    echo "⏳ $(get_msg processing_php)" >&2
    show_loading $PROCESS_PID ""
elif [ "${VERBOSE:-false}" = "true" ]; then
    echo "⏳ $(get_msg processing)" >&2
    show_loading $PROCESS_PID ""
    echo "✅" >&2
else
    show_loading $PROCESS_PID "⏳ $(get_msg processing)"
fi

OUTPUT="$(cat /tmp/composer-process-$$.out 2>/dev/null || true)"
rm -f /tmp/composer-process-$$.out
OUTPUT="$(printf "%s\n" "$OUTPUT" | grep -v '^Warning:' || true)"

[ "$DEBUG" = "true" ] && echo "🔍 $(get_msg debug_prefix)$(get_msg debug_output_length) ${#OUTPUT} characters" >&2

# Extract commands
COMMANDS=""
printf "%s\n" "$OUTPUT" | grep -q "^---COMMANDS_START---" && COMMANDS="$(printf "%s\n" "$OUTPUT" | sed -n '/^---COMMANDS_START---$/,/^---COMMANDS_END---$/p' | grep -v '^---' || true)"
OUTPUT="$(printf "%s\n" "$OUTPUT" | sed '/^---COMMANDS_START---$/,/^---COMMANDS_END---$/d' || true)"

# Display output
printf "%s\n" "$OUTPUT"

# Execute if --run
[ "$RUN_FLAG" = "--run" ] && [ -n "$COMMANDS" ] && {
    echo ""; echo "🚀  $(get_msg running)"
    printf "%s\n" "$COMMANDS" | while IFS= read -r cmd; do
        [ -z "$cmd" ] && continue
        echo "→ $cmd"
        sh -lc "$PHP_BIN -d date.timezone=UTC $COMPOSER_BIN $(printf '%s' "$cmd" | sed 's/^composer //')"
    done
    echo "✅  $(get_msg update_completed)"
}
