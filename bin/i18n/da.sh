#!/bin/bash
# Danish translations
#
# This file contains Danish translations for bash scripts
# Format: declare -A TRANSLATIONS_DA=([key]='value' ...)

declare -A TRANSLATIONS_DA=(
    # Main messages
    ['loading_config']='Indlæser konfiguration...'
    ['checking_outdated']='Tjekker forældede pakker...'
    ['processing']='Behandler pakker...'
    ['processing_php']='Behandler pakker med PHP-script...'
    ['running']='Kører...'
    ['update_completed']='Opdatering fuldført.'
    ['no_outdated']='Ingen forældede direkte afhængigheder.'

    # Configuration
    ['found_config']='Konfigurationsfil fundet: '
    ['no_config']='Ingen konfigurationsfil fundet (bruger standardværdier)'

    # Errors
    ['composer_not_found']='Composer er ikke installeret eller findes ikke i PATH.'
    ['composer_json_not_found']='composer.json blev ikke fundet i den aktuelle mappe.'
    ['processor_not_found']='Kunne ikke finde process-updates.php i vendor eller scriptmappen.'
    ['please_install']='Kør: composer install'
    ['unknown_option']='Ukendt option:'
    ['use_help']='Brug --help eller -h for brugsoplysninger.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Aktuel mappe:'
    ['debug_searching_config']='Søger efter konfigurationsfiler:'
    ['debug_composer_executed']='Composer outdated kommando udført'
    ['debug_json_length']='OUTDATED_JSON længde:'
    ['debug_empty_json']='Composer outdated returnerede tom JSON'
    ['debug_passing_to_php']='Videresender til PHP-script:'
    ['debug_output_length']='PHP-script outputlængde:'
    ['debug_processor_found']='PHP-processor fundet i:'
)

