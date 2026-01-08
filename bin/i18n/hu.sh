#!/bin/bash
# Hungarian translations
#
# This file contains Hungarian translations for bash scripts
# Format: declare -A TRANSLATIONS_HU=([key]='value' ...)

declare -A TRANSLATIONS_HU=(
    # Main messages
    ['loading_config']='Konfiguráció betöltése...'
    ['checking_outdated']='Elavult csomagok ellenőrzése...'
    ['processing']='Csomagok feldolgozása...'
    ['processing_php']='Csomagok feldolgozása PHP szkripttel...'
    ['running']='Futtatás...'
    ['update_completed']='Frissítés befejezve.'
    ['no_outdated']='Nincs elavult közvetlen függőség.'

    # Configuration
    ['found_config']='Konfigurációs fájl találva: '
    ['no_config']='Nem található konfigurációs fájl (alapértelmezett értékek használata)'

    # Errors
    ['composer_not_found']='A Composer nincs telepítve vagy nincs a PATH-ban.'
    ['composer_json_not_found']='composer.json nem található az aktuális könyvtárban.'
    ['processor_not_found']='Nem található process-updates.php a vendor vagy szkript könyvtárban.'
    ['please_install']='Futtassa: composer install'
    ['unknown_option']='Ismeretlen opció:'
    ['use_help']='Használja a --help vagy -h opciót a használati információkhoz.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Aktuális könyvtár:'
    ['debug_searching_config']='Konfigurációs fájlok keresése:'
    ['debug_composer_executed']='Composer outdated parancs végrehajtva'
    ['debug_json_length']='OUTDATED_JSON hossza:'
    ['debug_empty_json']='Composer outdated üres JSON-t adott vissza'
    ['debug_passing_to_php']='Átadás PHP szkriptnek:'
    ['debug_output_length']='PHP szkript kimenet hossza:'
    ['debug_processor_found']='PHP processzor találva:'
)

