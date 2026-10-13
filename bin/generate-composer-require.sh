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

# Helper function to read YAML config value using PHP (if available)
read_yaml_config_value() {
    local yaml_file="$1"
    local key="$2"
    local default="$3"

    if [ ! -f "$yaml_file" ]; then
        echo "$default"
        return
    fi

    # Try to use PHP if available (more reliable)
    if command -v php >/dev/null 2>&1; then
        # Values are passed as argv (never interpolated into the PHP source)
        # shellcheck disable=SC2016
        php -r '
        [, $file, $key, $default] = $argv;
        $content = @file_get_contents($file);
        if (!$content) { echo $default; exit; }
        foreach (explode("\n", $content) as $line) {
            $line = trim($line);
            if ($line === "" || strpos($line, "#") === 0) continue;
            if (preg_match("/^" . preg_quote($key, "/") . ":\\s*(.+)\$/", $line, $matches)) {
                $value = trim($matches[1]);
                if (strtolower($value) === "true") { echo "true"; exit; }
                if (strtolower($value) === "false") { echo "false"; exit; }
                echo $value; exit;
            }
        }
        echo $default;
        ' -- "$yaml_file" "$key" "$default" 2>/dev/null || echo "$default"
    else
        # Fallback: simple grep (less reliable)
        local value=$(grep -E "^${key}:" "$yaml_file" 2>/dev/null | head -1 | sed 's/^[^:]*:[[:space:]]*//' | tr -d '\n')
        if [ -n "$value" ]; then
            echo "$value"
        else
            echo "$default"
        fi
    fi
}

# Detect config file early (before parsing arguments) to read defaults
config_info=$(detect_config_file)
CONFIG_FILE=$(echo "$config_info" | cut -d'|' -f1)
CONFIG_FILE_DISPLAY=$(echo "$config_info" | cut -d'|' -f2)
CONFIG_FILE_SUFFIX=$(echo "$config_info" | cut -d'|' -f3)

# Read default values from YAML config (if config file exists)
if [ -n "$CONFIG_FILE" ] && [ -f "$CONFIG_FILE" ]; then
    # Read boolean config values (default to false)
    YAML_SHOW_RELEASE_INFO=$(read_yaml_config_value "$CONFIG_FILE" "show-release-info" "false")
    YAML_SHOW_RELEASE_DETAIL=$(read_yaml_config_value "$CONFIG_FILE" "show-release-detail" "false")
    YAML_SHOW_IMPACT_ANALYSIS=$(read_yaml_config_value "$CONFIG_FILE" "show-impact-analysis" "false")
    YAML_SAVE_IMPACT_TO_FILE=$(read_yaml_config_value "$CONFIG_FILE" "save-impact-to-file" "false")
    YAML_VERBOSE=$(read_yaml_config_value "$CONFIG_FILE" "verbose" "false")
    YAML_DEBUG=$(read_yaml_config_value "$CONFIG_FILE" "debug" "false")
else
    # No config file, use defaults
    YAML_SHOW_RELEASE_INFO="false"
    YAML_SHOW_RELEASE_DETAIL="false"
    YAML_SHOW_IMPACT_ANALYSIS="false"
    YAML_SAVE_IMPACT_TO_FILE="false"
    YAML_VERBOSE="false"
    YAML_DEBUG="false"
fi

# Convert YAML values to boolean (normalize true/false strings)
[ "$YAML_SHOW_RELEASE_INFO" = "true" ] && SHOW_RELEASE_INFO=true || SHOW_RELEASE_INFO=false
[ "$YAML_SHOW_RELEASE_DETAIL" = "true" ] && SHOW_RELEASE_DETAIL=true || SHOW_RELEASE_DETAIL=false
[ "$YAML_SHOW_IMPACT_ANALYSIS" = "true" ] && SHOW_IMPACT_ANALYSIS=true || SHOW_IMPACT_ANALYSIS=false
[ "$YAML_SAVE_IMPACT_TO_FILE" = "true" ] && SAVE_IMPACT_TO_FILE=true || SAVE_IMPACT_TO_FILE=false
# If save-impact-to-file is enabled, automatically enable show-impact-analysis (like --save-impact flag does)
[ "$YAML_SAVE_IMPACT_TO_FILE" = "true" ] && SHOW_IMPACT_ANALYSIS=true
[ "$YAML_VERBOSE" = "true" ] && VERBOSE=true || VERBOSE=false
[ "$YAML_DEBUG" = "true" ] && DEBUG=true || DEBUG=false

# Parse arguments (command line arguments override YAML defaults)
RUN_FLAG=""
for arg in "$@"; do
    case "$arg" in
        -h|--help) show_help; exit 0 ;;
        --run) RUN_FLAG="--run" ;;
        --release-info|--releases) SHOW_RELEASE_INFO=true ;;
        --release-detail|--release-full|--detail) SHOW_RELEASE_INFO=true; SHOW_RELEASE_DETAIL=true ;;
        --show-impact|--impact) SHOW_IMPACT_ANALYSIS=true ;;
        --save-impact) SAVE_IMPACT_TO_FILE=true; SHOW_IMPACT_ANALYSIS=true ;;
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

# Config file already detected above (before parsing arguments)
# CONFIG_FILE, CONFIG_FILE_DISPLAY, CONFIG_FILE_SUFFIX are already set

# Initialize language for translations after detecting config file
if command -v t >/dev/null 2>&1 && command -v load_translations_sh >/dev/null 2>&1; then
    # Force re-detection of language now that CONFIG_FILE is set
    detected_lang=$(detect_language_for_script)
    # Initialize translations with detected language
    if [ -n "$detected_lang" ]; then
        # Find i18n directory
        i18n_paths="
vendor/nowo-tech/composer-update-helper/bin/i18n
${SCRIPT_DIR}/i18n
$(dirname "$(dirname "$SCRIPT_DIR")")/nowo-tech/composer-update-helper/bin/i18n
"
        i18n_dir=""
        for path in $i18n_paths; do
            if [ -f "${path}/en.sh" ] 2>/dev/null; then
                i18n_dir="$path"
                break
            fi
        done
        if [ -n "$i18n_dir" ]; then
            load_translations_sh "$detected_lang" "$i18n_dir"
        fi
    fi
fi

# Show config info
if [ -n "$CONFIG_FILE" ]; then
    [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ] && echo "📋 $(get_msg found_config)$CONFIG_FILE_DISPLAY$CONFIG_FILE_SUFFIX" >&2
    [ "$DEBUG" != "true" ] && [ "$VERBOSE" != "true" ] && printf "⏳ $(get_msg loading_config)✅\n" >&2
    [ "$DEBUG" = "true" ] && echo "⏳ $(get_msg loading_config)" >&2
else
    [ "$VERBOSE" = "true" ] || [ "$DEBUG" = "true" ] && echo "ℹ️  $(get_msg no_config)" >&2
fi

# Private temporary files (mktemp, not predictable /tmp/<name>-$$ paths), removed on exit
OUTDATED_OUT="$(mktemp "${TMPDIR:-/tmp}/composer-outdated.XXXXXX")"
OUTDATED_ERR="$(mktemp "${TMPDIR:-/tmp}/composer-outdated-err.XXXXXX")"
PROCESS_OUT="$(mktemp "${TMPDIR:-/tmp}/composer-process.XXXXXX")"
trap 'rm -f "$OUTDATED_OUT" "$OUTDATED_ERR" "$PROCESS_OUT"' EXIT
trap 'exit 130' INT TERM

# Run composer outdated (stdout = JSON only; stderr kept apart so notices cannot corrupt the JSON)
"$PHP_BIN" -d date.timezone=UTC "$COMPOSER_BIN" outdated --direct --format=json > "$OUTDATED_OUT" 2> "$OUTDATED_ERR" &
OUTDATED_PID=$!
OUTDATED_STATUS=0

if [ "${DEBUG:-false}" = "true" ]; then
    echo "⏳ $(get_msg checking_outdated)" >&2
    show_loading $OUTDATED_PID "" || OUTDATED_STATUS=$?
elif [ "${VERBOSE:-false}" = "true" ]; then
    echo "⏳ $(get_msg checking_outdated)" >&2
    show_loading $OUTDATED_PID "" || OUTDATED_STATUS=$?
    echo "✅" >&2
else
    show_loading $OUTDATED_PID "⏳ $(get_msg checking_outdated)" || OUTDATED_STATUS=$?
fi

OUTDATED_JSON="$(grep -v '^Warning:' "$OUTDATED_OUT" 2>/dev/null || true)"

[ "$DEBUG" = "true" ] && echo "🔍 $(get_msg debug_prefix)$(get_msg debug_composer_executed)" >&2
[ "$DEBUG" = "true" ] && echo "🔍 $(get_msg debug_prefix)$(get_msg debug_json_length) ${#OUTDATED_JSON} characters" >&2
[ "$DEBUG" = "true" ] && [ -s "$OUTDATED_ERR" ] && sed 's/^/   composer: /' "$OUTDATED_ERR" >&2

if [ "$OUTDATED_STATUS" -ne 0 ]; then
    echo "❌  composer outdated failed (exit code $OUTDATED_STATUS):" >&2
    cat "$OUTDATED_ERR" >&2
    exit "$OUTDATED_STATUS"
fi

if [ -z "${OUTDATED_JSON}" ]; then
    [ "$DEBUG" = "true" ] && echo "🔍 $(get_msg debug_prefix)$(get_msg debug_empty_json)" >&2
    echo "❌  composer outdated returned no JSON output." >&2
    cat "$OUTDATED_ERR" >&2
    exit 1
fi
# "composer outdated" returns {"installed": []} when everything is up to date
if printf "%s" "$OUTDATED_JSON" | tr -d ' \t\r\n' | grep -q '"installed":\[\]'; then
    echo "✅  $(get_msg no_outdated)"
    exit 0
fi

# Process with PHP
[ "$DEBUG" = "true" ] && echo "🔍 $(get_msg debug_prefix)$(get_msg debug_passing_to_php)" >&2
[ "$DEBUG" = "true" ] && echo "   - CONFIG_FILE: ${CONFIG_FILE:-none}" >&2
[ "$DEBUG" = "true" ] && echo "   - SHOW_RELEASE_INFO: $SHOW_RELEASE_INFO" >&2
[ "$DEBUG" = "true" ] && echo "   - SHOW_IMPACT_ANALYSIS: $SHOW_IMPACT_ANALYSIS" >&2
[ "$DEBUG" = "true" ] && echo "   - SAVE_IMPACT_TO_FILE: $SAVE_IMPACT_TO_FILE" >&2
[ "$DEBUG" = "true" ] && echo "   - DEBUG: $DEBUG" >&2
[ "$DEBUG" = "true" ] && echo "   - PROCESSOR_PHP: $PROCESSOR_PHP" >&2

# Get script directory for impact file (where the script is located)
IMPACT_FILE_DIR="$(dirname "$0")"
[ "$DEBUG" = "true" ] && echo "   - IMPACT_FILE_DIR: $IMPACT_FILE_DIR" >&2

# The processor's stderr (error messages) is shown with its output; its exit status is propagated
OUTDATED_JSON="$OUTDATED_JSON" COMPOSER_BIN="$COMPOSER_BIN" PHP_BIN="$PHP_BIN" CONFIG_FILE="$CONFIG_FILE" SHOW_RELEASE_INFO="$SHOW_RELEASE_INFO" SHOW_IMPACT_ANALYSIS="$SHOW_IMPACT_ANALYSIS" SAVE_IMPACT_TO_FILE="$SAVE_IMPACT_TO_FILE" IMPACT_FILE_DIR="$IMPACT_FILE_DIR" DEBUG="$DEBUG" VERBOSE="$VERBOSE" "$PHP_BIN" -d date.timezone=UTC "$PROCESSOR_PHP" > "$PROCESS_OUT" 2>&1 &
PROCESS_PID=$!
PROCESS_STATUS=0

if [ "${DEBUG:-false}" = "true" ]; then
    echo "⏳ $(get_msg processing_php)" >&2
    show_loading $PROCESS_PID "" || PROCESS_STATUS=$?
elif [ "${VERBOSE:-false}" = "true" ]; then
    echo "⏳ $(get_msg processing)" >&2
    show_loading $PROCESS_PID "" || PROCESS_STATUS=$?
    echo "✅" >&2
else
    show_loading $PROCESS_PID "⏳ $(get_msg processing)" || PROCESS_STATUS=$?
fi

OUTPUT="$(grep -v '^Warning:' "$PROCESS_OUT" 2>/dev/null || true)"

if [ "$PROCESS_STATUS" -ne 0 ]; then
    printf "%s\n" "$OUTPUT" >&2
    echo "❌  process-updates.php failed (exit code $PROCESS_STATUS)." >&2
    exit "$PROCESS_STATUS"
fi

[ "$DEBUG" = "true" ] && echo "🔍 $(get_msg debug_prefix)$(get_msg debug_output_length) ${#OUTPUT} characters" >&2

# Extract commands
COMMANDS=""
printf "%s\n" "$OUTPUT" | grep -q "^---COMMANDS_START---" && COMMANDS="$(printf "%s\n" "$OUTPUT" | sed -n '/^---COMMANDS_START---$/,/^---COMMANDS_END---$/p' | grep -v '^---' || true)"
OUTPUT="$(printf "%s\n" "$OUTPUT" | sed '/^---COMMANDS_START---$/,/^---COMMANDS_END---$/d' || true)"

# Display output
printf "%s\n" "$OUTPUT"

# Execute if --run (argv only — no shell interpolation of composer args)
if [ "$RUN_FLAG" = "--run" ] && [ -n "$COMMANDS" ]; then
    echo ""; echo "🚀  $(get_msg running)"
    printf "%s\n" "$COMMANDS" | while IFS= read -r cmd; do
        [ -z "$cmd" ] && continue
        echo "→ $cmd"
        # Strip leading "composer " then split into argv for safe execution
        args="${cmd#composer }"
        # Disable glob so constraints like "symfony/*" are not expanded
        set -f
        # shellcheck disable=SC2086
        set -- $args
        set +f
        "$PHP_BIN" -d date.timezone=UTC "$COMPOSER_BIN" "$@"
    done
    echo "✅  $(get_msg update_completed)"
fi

exit 0
