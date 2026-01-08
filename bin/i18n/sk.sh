#!/bin/bash
# Slovak translations
#
# This file contains Slovak translations for bash scripts
# Format: declare -A TRANSLATIONS_SK=([key]='value' ...)

declare -A TRANSLATIONS_SK=(
    # Main messages
    ['loading_config']='Načítanie konfigurácie...'
    ['checking_outdated']='Kontrola zastaralých balíčkov...'
    ['processing']='Spracovanie balíčkov...'
    ['processing_php']='Spracovanie balíčkov pomocou PHP skriptu...'
    ['running']='Spúšťanie...'
    ['update_completed']='Aktualizácia dokončená.'
    ['no_outdated']='Žiadne zastaralé priame závislosti.'

    # Configuration
    ['found_config']='Nájdený konfiguračný súbor: '
    ['no_config']='Nenájdený konfiguračný súbor (použitie predvolených hodnôt)'

    # Errors
    ['composer_not_found']='Composer nie je nainštalovaný alebo nie je v PATH.'
    ['composer_json_not_found']='composer.json nenájdený v aktuálnom adresári.'
    ['processor_not_found']='Nemožno nájsť process-updates.php vo vendor alebo adresári skriptov.'
    ['please_install']='Spustite: composer install'
    ['unknown_option']='Neznáma voľba:'
    ['use_help']='Použite --help alebo -h pre informácie o použití.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Aktuálny adresár:'
    ['debug_searching_config']='Vyhľadávanie konfiguračných súborov:'
    ['debug_composer_executed']='Príkaz composer outdated vykonaný'
    ['debug_json_length']='Dĺžka OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated vrátil prázdny JSON'
    ['debug_passing_to_php']='Odovzdávanie do PHP skriptu:'
    ['debug_output_length']='Dĺžka výstupu PHP skriptu:'
    ['debug_processor_found']='PHP procesor nájdený na:'
)

