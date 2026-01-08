#!/bin/sh
# Translation loader for sh (POSIX-compatible)
#
# This file contains functions to load and use translations in sh scripts
# Compatible with /bin/sh (not just bash)
#
# Usage:
#   . bin/i18n/translations-sh.sh
#   t "loading_config"

# Detect system language
detect_language() {
    # Try to get language from environment variables (in order of priority)
    local lang="${LC_MESSAGES:-${LC_ALL:-${LANG:-en_US}}}"

    # Extract language code (e.g., "es_ES.UTF-8" -> "es", "en_US.UTF-8" -> "en")
    local lang_code=$(echo "$lang" | cut -d'_' -f1 | tr '[:upper:]' '[:lower:]')

    # Supported languages (31 total - all implemented)
    local supported="en es pt it fr de pl ru ro el da nl cs sv no fi tr zh ja ko ar hu sk uk hr bg he hi vi id th"

    # Check if language is supported
    if echo "$supported" | grep -q "$lang_code"; then
        echo "$lang_code"
    else
        echo "en"
    fi
}

# Get translation value from a language file (sh-compatible, no arrays)
get_translation_from_file() {
    local lang="$1"
    local key="$2"
    local i18n_dir="$3"
    local translation_file="${i18n_dir}/${lang}.sh"

    # Try to read the translation file and extract the value
    if [ -f "$translation_file" ]; then
        # Extract value from array declaration (compatible with bash arrays)
        # Pattern: ['key']='value' or ['key']="value"
        # Match lines like: ['loading_config']='Loading configuration...'
        # Use sed with proper escaping - handle both single and double quotes
        grep "^\s*\['${key}'\]=" "$translation_file" 2>/dev/null | \
            sed -E "s/.*\['${key}'\]=['\"](.*)['\"].*/\1/" | \
            head -1
    fi
}

# Load translations for a specific language (sh-compatible wrapper)
load_translations_sh() {
    local lang="${1:-en}"
    local i18n_dir="${2:-}"

    # If directory not provided, try to detect it
    if [ -z "$i18n_dir" ]; then
        # Try to get from BASH_SOURCE if available (bash), otherwise try $0
        if [ -n "${BASH_SOURCE[0]:-}" ]; then
            i18n_dir="$(dirname "${BASH_SOURCE[0]}")"
        else
            # For sh, try to find the directory by looking for en.sh
            for path in "$(dirname "$0")" "vendor/nowo-tech/composer-update-helper/bin/i18n" "$(dirname "$(dirname "$0")")/nowo-tech/composer-update-helper/bin/i18n" "./bin/i18n" "../bin/i18n"; do
                if [ -f "${path}/en.sh" ] 2>/dev/null; then
                    i18n_dir="$path"
                    break
                fi
            done
        fi
    fi

    # Store language and directory for later use
    export TRANSLATIONS_LANG="$lang"
    export TRANSLATIONS_DIR="$i18n_dir"
    export TRANSLATIONS_LOADED="$lang"
}

# Translate a message (sh-compatible, uses file reading instead of arrays)
t() {
    local key="$1"
    local lang="${TRANSLATIONS_LANG:-}"
    local i18n_dir="${TRANSLATIONS_DIR:-}"

    # If language not set, detect it
    if [ -z "$lang" ]; then
        # Try to get from config file first
        local config_file="${CONFIG_FILE:-}"
        if [ -n "$config_file" ] && [ -f "$config_file" ]; then
            lang=$(grep -E "^language:" "$config_file" 2>/dev/null | sed 's/^language:[[:space:]]*//' | tr -d '[:space:]' || echo "")
        fi

        # If not in config, detect from system
        if [ -z "$lang" ]; then
            lang=$(detect_language)
        fi
    fi

    # If directory not set, try to find it
    if [ -z "$i18n_dir" ]; then
        # Try multiple paths
        for path in "$(dirname "$0")" "vendor/nowo-tech/composer-update-helper/bin/i18n" "$(dirname "$(dirname "$0")")/nowo-tech/composer-update-helper/bin/i18n"; do
            if [ -f "${path}/en.sh" ]; then
                i18n_dir="$path"
                break
            fi
        done
    fi

    # Load translations if not already loaded
    if [ -z "${TRANSLATIONS_LOADED:-}" ] || [ "${TRANSLATIONS_LOADED}" != "$lang" ]; then
        load_translations_sh "$lang" "$i18n_dir"
        i18n_dir="${TRANSLATIONS_DIR:-$i18n_dir}"
    fi

    # Get translation from file
    local translation=$(get_translation_from_file "$lang" "$key" "$i18n_dir")

    # If translation not found, try English as fallback
    if [ -z "$translation" ] && [ "$lang" != "en" ]; then
        translation=$(get_translation_from_file "en" "$key" "$i18n_dir")
    fi

    # If still not found, return the key
    if [ -z "$translation" ]; then
        echo "$key"
    else
        echo "$translation"
    fi
}

# Initialize translations on source
TRANSLATIONS_LANG=""
TRANSLATIONS_DIR=""
TRANSLATIONS_LOADED=""

