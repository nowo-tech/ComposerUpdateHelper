#!/bin/bash
# Italian translations
#
# This file contains Italian translations for bash scripts
# Format: declare -A TRANSLATIONS_IT=([key]='value' ...)

declare -A TRANSLATIONS_IT=(
    # Main messages
    ['loading_config']='Caricamento configurazione...'
    ['checking_outdated']='Controllo pacchetti obsoleti...'
    ['processing']='Elaborazione pacchetti...'
    ['processing_php']='Elaborazione pacchetti con script PHP...'
    ['running']='Esecuzione...'
    ['update_completed']='Aggiornamento completato.'
    ['no_outdated']='Nessuna dipendenza diretta obsoleta.'

    # Configuration
    ['found_config']='File di configurazione trovato: '
    ['no_config']='Nessun file di configurazione trovato (uso dei valori predefiniti)'

    # Errors
    ['composer_not_found']='Composer non è installato o non è nel PATH.'
    ['composer_json_not_found']='composer.json non trovato nella directory corrente.'
    ['processor_not_found']='Impossibile trovare process-updates.php in vendor o directory degli script.'
    ['please_install']='Eseguire: composer install'
    ['unknown_option']='Opzione sconosciuta:'
    ['use_help']='Usare --help o -h per informazioni sull\'utilizzo.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Directory corrente:'
    ['debug_searching_config']='Ricerca file di configurazione:'
    ['debug_composer_executed']='Comando composer outdated eseguito'
    ['debug_json_length']='Lunghezza di OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated ha restituito JSON vuoto'
    ['debug_passing_to_php']='Passaggio a script PHP:'
    ['debug_output_length']='Lunghezza dell\'output dello script PHP:'
    ['debug_processor_found']='Processore PHP trovato in:'
)

