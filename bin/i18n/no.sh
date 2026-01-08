#!/bin/bash
# Norwegian translations
#
# This file contains Norwegian translations for bash scripts
# Format: declare -A TRANSLATIONS_NO=([key]='value' ...)

declare -A TRANSLATIONS_NO=(
    # Main messages
    ['loading_config']='Laster konfigurasjon...'
    ['checking_outdated']='Sjekker utdaterte pakker...'
    ['processing']='Behandler pakker...'
    ['processing_php']='Behandler pakker med PHP-skript...'
    ['running']='Kjører...'
    ['update_completed']='Oppdatering fullført.'
    ['no_outdated']='Ingen utdaterte direkte avhengigheter.'

    # Configuration
    ['found_config']='Konfigurasjonsfil funnet: '
    ['no_config']='Ingen konfigurasjonsfil funnet (bruker standardverdier)'

    # Errors
    ['composer_not_found']='Composer er ikke installert eller ikke i PATH.'
    ['composer_json_not_found']='composer.json ikke funnet i gjeldende katalog.'
    ['processor_not_found']='Kunne ikke finne process-updates.php i vendor eller skriptkatalog.'
    ['please_install']='Kjør: composer install'
    ['unknown_option']='Ukjent alternativ:'
    ['use_help']='Bruk --help eller -h for bruksinformasjon.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Gjeldende katalog:'
    ['debug_searching_config']='Søker etter konfigurasjonsfiler:'
    ['debug_composer_executed']='Composer outdated kommando utført'
    ['debug_json_length']='OUTDATED_JSON lengde:'
    ['debug_empty_json']='Composer outdated returnerte tom JSON'
    ['debug_passing_to_php']='Sender til PHP-skript:'
    ['debug_output_length']='PHP-skript utdata lengde:'
    ['debug_processor_found']='PHP-prosessor funnet på:'
)

