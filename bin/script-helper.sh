#!/bin/sh
# Script helper for generate-composer-require.sh
# Contains helper functions that can be loaded from vendor
# This keeps the main script lightweight

# Function to detect language for help/config
detect_language_for_script() {
    local lang="en"

    # Try to get from config file first
    local config_file="${CONFIG_FILE:-}"

    # If CONFIG_FILE is not set, try to detect it
    if [ -z "$config_file" ]; then
        # Check for .yaml first, then .yml, then .txt
        if [ -f "generate-composer-require.yaml" ]; then
            config_file="generate-composer-require.yaml"
        elif [ -f "generate-composer-require.yml" ]; then
            config_file="generate-composer-require.yml"
        elif [ -f "generate-composer-require.ignore.txt" ]; then
            config_file="generate-composer-require.ignore.txt"
        fi
    fi

    # Read language from config file if it exists
    if [ -n "$config_file" ] && [ -f "$config_file" ]; then
        lang=$(grep -E "^language:" "$config_file" 2>/dev/null | sed 's/^language:[[:space:]]*//' | tr -d '[:space:]' || echo "")
    fi

    # If not in config, detect from system
    if [ -z "$lang" ] && command -v detect_language >/dev/null 2>&1; then
        lang=$(detect_language)
    elif [ -z "$lang" ]; then
        # Fallback detection without i18n loader
        local sys_lang="${LC_MESSAGES:-${LC_ALL:-${LANG:-en_US}}}"
        lang=$(echo "$sys_lang" | cut -d'_' -f1 | tr '[:upper:]' '[:lower:]')
        local supported="en es pt it fr de pl ru ro el da nl cs sv no fi tr zh ja ko ar hu sk uk hr bg he hi vi id th"
        if ! echo "$supported" | grep -q "$lang"; then
            lang="en"
        fi
    fi

    echo "$lang"
}

# Function to find file in multiple possible locations
find_file_in_paths() {
    local paths="$1"
    local found_file=""

    for path in $paths; do
        if [ -f "$path" ]; then
            found_file="$path"
            break
        fi
    done

    echo "$found_file"
}

# Function to detect configuration file
detect_config_file() {
    local debug="${DEBUG:-false}"

    # Debug: Show current directory and files being searched
    if [ "$debug" = "true" ]; then
        echo "DEBUG: Current directory: $(pwd)" >&2
        echo "DEBUG: Searching for configuration files:" >&2
        echo "   - generate-composer-require.yaml" >&2
        echo "   - generate-composer-require.yml" >&2
        echo "   - generate-composer-require.ignore.txt" >&2
    fi

    # Check for .yaml first, then .yml, then .txt
    if [ -f "generate-composer-require.yaml" ]; then
        echo "generate-composer-require.yaml|generate-composer-require.yaml|"
    elif [ -f "generate-composer-require.yml" ]; then
        echo "generate-composer-require.yml|generate-composer-require.yml|"
    elif [ -f "generate-composer-require.ignore.txt" ]; then
        # Fallback to old TXT format for backward compatibility
        echo "generate-composer-require.ignore.txt|generate-composer-require.ignore.txt| (old format)"
    else
        echo "||"
    fi
}

# Function to show loading indicator or spinner
show_loading() {
    local pid=$1
    local message=$2
    local debug="${DEBUG:-false}"
    local verbose="${VERBOSE:-false}"
    local spinstr='|/-\'
    local temp

    # In debug or verbose mode, just wait without spinner
    if [ "$debug" = "true" ] || [ "$verbose" = "true" ]; then
        wait $pid
        return $?
    fi

    # Show animated spinner
    echo -n "$message" >&2
    while kill -0 $pid 2>/dev/null; do
        temp=${spinstr#?}
        printf "\b${spinstr:0:1}" >&2
        spinstr=$temp${spinstr%"$temp"}
        sleep 0.1
    done
    printf "\b✅\n" >&2
    wait $pid
    return $?
}

# Function to get help file path
get_help_file_path() {
    local lang="$1"
    local script_dir="$2"

    # Build paths to search
    local paths="
${script_dir}/i18n/help-${lang}.txt
vendor/nowo-tech/composer-update-helper/bin/i18n/help-${lang}.txt
$(dirname "$(dirname "$script_dir")")/nowo-tech/composer-update-helper/bin/i18n/help-${lang}.txt
"
    find_file_in_paths "$paths"
}

