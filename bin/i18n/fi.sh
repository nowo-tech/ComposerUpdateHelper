#!/bin/bash
# Finnish translations
#
# This file contains Finnish translations for bash scripts
# Format: declare -A TRANSLATIONS_FI=([key]='value' ...)

declare -A TRANSLATIONS_FI=(
    # Main messages
    ['loading_config']='Ladataan konfiguraatiota...'
    ['checking_outdated']='Tarkistetaan vanhentuneita paketteja...'
    ['processing']='Käsitellään paketteja...'
    ['processing_php']='Käsitellään paketteja PHP-skriptillä...'
    ['running']='Suoritetaan...'
    ['update_completed']='Päivitys valmis.'
    ['no_outdated']='Ei vanhentuneita suoria riippuvuuksia.'

    # Configuration
    ['found_config']='Konfiguraatiotiedosto löytyi: '
    ['no_config']='Konfiguraatiotiedostoa ei löytynyt (käytetään oletusarvoja)'

    # Errors
    ['composer_not_found']='Composer ei ole asennettu tai ei ole PATHissa.'
    ['composer_json_not_found']='composer.json ei löytynyt nykyisestä hakemistosta.'
    ['processor_not_found']='Ei löytynyt process-updates.php vendor- tai skriptihakemistosta.'
    ['please_install']='Suorita: composer install'
    ['unknown_option']='Tuntematon vaihtoehto:'
    ['use_help']='Käytä --help tai -h saadaksesi käyttöohjeet.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Nykyinen hakemisto:'
    ['debug_searching_config']='Etsitään konfiguraatiotiedostoja:'
    ['debug_composer_executed']='Composer outdated -komento suoritettu'
    ['debug_json_length']='OUTDATED_JSON pituus:'
    ['debug_empty_json']='Composer outdated palautti tyhjän JSONin'
    ['debug_passing_to_php']='Välitetään PHP-skriptille:'
    ['debug_output_length']='PHP-skriptin tulosteen pituus:'
    ['debug_processor_found']='PHP-prosessori löytyi osoitteesta:'
)

