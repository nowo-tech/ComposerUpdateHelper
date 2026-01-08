#!/bin/bash
# Romanian translations
#
# This file contains Romanian translations for bash scripts
# Format: declare -A TRANSLATIONS_RO=([key]='value' ...)

declare -A TRANSLATIONS_RO=(
    # Main messages
    ['loading_config']='Încărcare configurație...'
    ['checking_outdated']='Verificare pachete învechite...'
    ['processing']='Procesare pachete...'
    ['processing_php']='Procesare pachete cu script PHP...'
    ['running']='Executare...'
    ['update_completed']='Actualizare finalizată.'
    ['no_outdated']='Nu există dependențe directe învechite.'

    # Configuration
    ['found_config']='Fișier de configurație găsit: '
    ['no_config']='Nu s-a găsit fișier de configurație (folosire valori implicite)'

    # Errors
    ['composer_not_found']='Composer nu este instalat sau nu se află în PATH.'
    ['composer_json_not_found']='composer.json nu a fost găsit în directorul curent.'
    ['processor_not_found']='Nu s-a putut găsi process-updates.php în vendor sau directorul de scripturi.'
    ['please_install']='Executați: composer install'
    ['unknown_option']='Opțiune necunoscută:'
    ['use_help']='Folosiți --help sau -h pentru informații despre utilizare.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Directorul curent:'
    ['debug_searching_config']='Căutare fișiere de configurație:'
    ['debug_composer_executed']='Comandă composer outdated executată'
    ['debug_json_length']='Lungime OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated a returnat JSON gol'
    ['debug_passing_to_php']='Transmitere către script PHP:'
    ['debug_output_length']='Lungime ieșire script PHP:'
    ['debug_processor_found']='Procesor PHP găsit în:'
)

