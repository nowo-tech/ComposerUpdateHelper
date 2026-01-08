#!/bin/bash
# German translations
#
# This file contains German translations for bash scripts
# Format: declare -A TRANSLATIONS_DE=([key]='value' ...)

declare -A TRANSLATIONS_DE=(
    # Main messages
    ['loading_config']='Lade Konfiguration...'
    ['checking_outdated']='Prüfe veraltete Pakete...'
    ['processing']='Verarbeite Pakete...'
    ['processing_php']='Verarbeite Pakete mit PHP-Skript...'
    ['running']='Ausführung...'
    ['update_completed']='Aktualisierung abgeschlossen.'
    ['no_outdated']='Keine veralteten direkten Abhängigkeiten.'

    # Configuration
    ['found_config']='Konfigurationsdatei gefunden: '
    ['no_config']='Keine Konfigurationsdatei gefunden (Verwendung der Standardwerte)'

    # Errors
    ['composer_not_found']='Composer ist nicht installiert oder nicht im PATH.'
    ['composer_json_not_found']='composer.json wurde im aktuellen Verzeichnis nicht gefunden.'
    ['processor_not_found']='process-updates.php konnte nicht in vendor oder im Skriptverzeichnis gefunden werden.'
    ['please_install']='Ausführen: composer install'
    ['unknown_option']='Unbekannte Option:'
    ['use_help']='Verwenden Sie --help oder -h für Nutzungsinformationen.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Aktuelles Verzeichnis:'
    ['debug_searching_config']='Suche nach Konfigurationsdateien:'
    ['debug_composer_executed']='Composer outdated Befehl ausgeführt'
    ['debug_json_length']='OUTDATED_JSON Länge:'
    ['debug_empty_json']='Composer outdated hat leeres JSON zurückgegeben'
    ['debug_passing_to_php']='Übergabe an PHP-Skript:'
    ['debug_output_length']='PHP-Skript Ausgabelänge:'
    ['debug_processor_found']='PHP-Prozessor gefunden in:'
)

