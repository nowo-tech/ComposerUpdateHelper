#!/bin/bash
# Swedish translations
#
# This file contains Swedish translations for bash scripts
# Format: declare -A TRANSLATIONS_SV=([key]='value' ...)

declare -A TRANSLATIONS_SV=(
    # Main messages
    ['loading_config']='Laddar konfiguration...'
    ['checking_outdated']='Kontrollerar föråldrade paket...'
    ['processing']='Bearbetar paket...'
    ['processing_php']='Bearbetar paket med PHP-skript...'
    ['running']='Kör...'
    ['update_completed']='Uppdatering slutförd.'
    ['no_outdated']='Inga föråldrade direkta beroenden.'

    # Configuration
    ['found_config']='Konfigurationsfil hittad: '
    ['no_config']='Ingen konfigurationsfil hittad (använder standardvärden)'

    # Errors
    ['composer_not_found']='Composer är inte installerat eller inte i PATH.'
    ['composer_json_not_found']='composer.json hittades inte i den aktuella katalogen.'
    ['processor_not_found']='Kunde inte hitta process-updates.php i vendor eller skriptkatalog.'
    ['please_install']='Kör: composer install'
    ['unknown_option']='Okänd option:'
    ['use_help']='Använd --help eller -h för användningsinformation.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Aktuell katalog:'
    ['debug_searching_config']='Söker efter konfigurationsfiler:'
    ['debug_composer_executed']='Composer outdated kommando utfört'
    ['debug_json_length']='OUTDATED_JSON längd:'
    ['debug_empty_json']='Composer outdated returnerade tom JSON'
    ['debug_passing_to_php']='Skickar till PHP-skript:'
    ['debug_output_length']='PHP-skript utdata längd:'
    ['debug_processor_found']='PHP-processor hittad på:'
)

