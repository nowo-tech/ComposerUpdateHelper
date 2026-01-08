#!/bin/bash
# Czech translations
#
# This file contains Czech translations for bash scripts
# Format: declare -A TRANSLATIONS_CS=([key]='value' ...)

declare -A TRANSLATIONS_CS=(
    # Main messages
    ['loading_config']='Načítání konfigurace...'
    ['checking_outdated']='Kontrola zastaralých balíčků...'
    ['processing']='Zpracování balíčků...'
    ['processing_php']='Zpracování balíčků pomocí PHP skriptu...'
    ['running']='Spouštění...'
    ['update_completed']='Aktualizace dokončena.'
    ['no_outdated']='Žádné zastaralé přímé závislosti.'

    # Configuration
    ['found_config']='Nalezen konfigurační soubor: '
    ['no_config']='Nenalezen konfigurační soubor (použití výchozích hodnot)'

    # Errors
    ['composer_not_found']='Composer není nainstalován nebo není v PATH.'
    ['composer_json_not_found']='composer.json nenalezen v aktuálním adresáři.'
    ['processor_not_found']='Nelze najít process-updates.php ve vendor nebo adresáři skriptů.'
    ['please_install']='Spusťte: composer install'
    ['unknown_option']='Neznámá volba:'
    ['use_help']='Použijte --help nebo -h pro informace o použití.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Aktuální adresář:'
    ['debug_searching_config']='Vyhledávání konfiguračních souborů:'
    ['debug_composer_executed']='Příkaz composer outdated proveden'
    ['debug_json_length']='Délka OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated vrátil prázdný JSON'
    ['debug_passing_to_php']='Předávání do PHP skriptu:'
    ['debug_output_length']='Délka výstupu PHP skriptu:'
    ['debug_processor_found']='PHP procesor nalezen na:'
)

