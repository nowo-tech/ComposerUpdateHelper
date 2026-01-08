#!/bin/bash
# English translations (default)
#
# This file contains English translations for bash scripts
# Format: declare -A TRANSLATIONS_EN=([key]='value' ...)

declare -A TRANSLATIONS_EN=(
    # Main messages
    ['loading_config']='Loading configuration...'
    ['checking_outdated']='Checking for outdated packages...'
    ['processing']='Processing packages...'
    ['processing_php']='Processing packages with PHP script...'
    ['running']='Running...'
    ['update_completed']='Update completed.'
    ['no_outdated']='No outdated direct dependencies.'

    # Configuration
    ['found_config']='Found configuration file: '
    ['no_config']='No configuration file found (using defaults)'

    # Errors
    ['composer_not_found']='Composer is not installed or not in PATH.'
    ['composer_json_not_found']='composer.json not found in the current directory.'
    ['processor_not_found']='Could not find process-updates.php in vendor or script directory.'
    ['please_install']='Please run: composer install'
    ['unknown_option']='Unknown option:'
    ['use_help']='Use --help or -h for usage information.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Current directory:'
    ['debug_searching_config']='Searching for configuration files:'
    ['debug_composer_executed']='Composer outdated command executed'
    ['debug_json_length']='OUTDATED_JSON length:'
    ['debug_empty_json']='Composer outdated returned empty JSON'
    ['debug_passing_to_php']='Passing to PHP script:'
    ['debug_output_length']='PHP script output length:'
    ['debug_processor_found']='Processor PHP found at:'
)

