#!/bin/bash
# Polish translations
#
# This file contains Polish translations for bash scripts
# Format: declare -A TRANSLATIONS_PL=([key]='value' ...)

declare -A TRANSLATIONS_PL=(
    # Main messages
    ['loading_config']='Ładowanie konfiguracji...'
    ['checking_outdated']='Sprawdzanie przestarzałych pakietów...'
    ['processing']='Przetwarzanie pakietów...'
    ['processing_php']='Przetwarzanie pakietów za pomocą skryptu PHP...'
    ['running']='Uruchamianie...'
    ['update_completed']='Aktualizacja zakończona.'
    ['no_outdated']='Brak przestarzałych bezpośrednich zależności.'

    # Configuration
    ['found_config']='Znaleziono plik konfiguracyjny: '
    ['no_config']='Nie znaleziono pliku konfiguracyjnego (używanie wartości domyślnych)'

    # Errors
    ['composer_not_found']='Composer nie jest zainstalowany lub nie znajduje się w PATH.'
    ['composer_json_not_found']='Nie znaleziono composer.json w bieżącym katalogu.'
    ['processor_not_found']='Nie można znaleźć process-updates.php w vendor lub katalogu skryptów.'
    ['please_install']='Uruchom: composer install'
    ['unknown_option']='Nieznana opcja:'
    ['use_help']='Użyj --help lub -h, aby uzyskać informacje o użyciu.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Bieżący katalog:'
    ['debug_searching_config']='Wyszukiwanie plików konfiguracyjnych:'
    ['debug_composer_executed']='Polecenie composer outdated wykonane'
    ['debug_json_length']='Długość OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated zwrócił pusty JSON'
    ['debug_passing_to_php']='Przekazywanie do skryptu PHP:'
    ['debug_output_length']='Długość wyjścia skryptu PHP:'
    ['debug_processor_found']='Procesor PHP znaleziony w:'
)

