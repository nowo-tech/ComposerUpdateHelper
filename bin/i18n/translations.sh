#!/bin/bash
# Translation loader for Bash
#
# This file contains functions to load and use translations in bash scripts
#
# Usage:
#   source bin/i18n/translations.sh
#   t "no_packages_update"

# Detect system language
detect_language() {
    # Try to get language from environment variables (in order of priority)
    local lang="${LC_MESSAGES:-${LC_ALL:-${LANG:-en_US}}}"

    # Extract language code (e.g., "es_ES.UTF-8" -> "es", "en_US.UTF-8" -> "en")
    local lang_code=$(echo "$lang" | cut -d'_' -f1 | tr '[:upper:]' '[:lower:]')

    # Supported languages
    local supported="en es pt it fr de"

    # Check if language is supported
    if echo "$supported" | grep -q "$lang_code"; then
        echo "$lang_code"
    else
        echo "en"
    fi
}

# Load translations for a specific language
load_translations() {
    local lang="${1:-en}"
    local i18n_dir="$(dirname "${BASH_SOURCE[0]}")"
    local translation_file="${i18n_dir}/${lang}.sh"

    # Source the translation file if it exists
    if [ -f "$translation_file" ]; then
        source "$translation_file"
    else
        # Fallback to English
        local en_file="${i18n_dir}/en.sh"
        if [ -f "$en_file" ]; then
            source "$en_file"
        fi
    fi
}

# Translate a message
t() {
    local key="$1"
    local lang="${TRANSLATION_LANG:-}"

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

    # Load translations if not already loaded
    if [ -z "${TRANSLATIONS_LOADED:-}" ] || [ "${TRANSLATIONS_LOADED}" != "$lang" ]; then
        load_translations "$lang"
        export TRANSLATIONS_LOADED="$lang"
    fi

    # Get translation from the appropriate array
    local translation_var="TRANSLATIONS_$(echo "$lang" | tr '[:lower:]' '[:upper:]')"
    eval "local translations=(\"\${${translation_var}[@]}\")"

    # Get translation value
    local translation="${translations[$key]:-$key}"

    echo "$translation"
}

# Initialize translations on source
TRANSLATION_LANG=""
TRANSLATIONS_LOADED=""

