#!/bin/bash
# Dutch translations
#
# This file contains Dutch translations for bash scripts
# Format: declare -A TRANSLATIONS_NL=([key]='value' ...)

declare -A TRANSLATIONS_NL=(
    # Main messages
    ['loading_config']='Configuratie laden...'
    ['checking_outdated']='Controleren op verouderde pakketten...'
    ['processing']='Pakketten verwerken...'
    ['processing_php']='Pakketten verwerken met PHP script...'
    ['running']='Uitvoeren...'
    ['update_completed']='Update voltooid.'
    ['no_outdated']='Geen verouderde directe afhankelijkheden.'

    # Configuration
    ['found_config']='Configuratiebestand gevonden: '
    ['no_config']='Geen configuratiebestand gevonden (standaardwaarden gebruiken)'

    # Errors
    ['composer_not_found']='Composer is niet geïnstalleerd of niet in PATH.'
    ['composer_json_not_found']='composer.json niet gevonden in de huidige directory.'
    ['processor_not_found']='Kon process-updates.php niet vinden in vendor of script directory.'
    ['please_install']='Voer uit: composer install'
    ['unknown_option']='Onbekende optie:'
    ['use_help']='Gebruik --help of -h voor gebruiks informatie.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Huidige directory:'
    ['debug_searching_config']='Zoeken naar configuratiebestanden:'
    ['debug_composer_executed']='Composer outdated commando uitgevoerd'
    ['debug_json_length']='OUTDATED_JSON lengte:'
    ['debug_empty_json']='Composer outdated retourneerde lege JSON'
    ['debug_passing_to_php']='Doorgeven aan PHP script:'
    ['debug_output_length']='PHP script uitvoer lengte:'
    ['debug_processor_found']='PHP processor gevonden op:'
)

