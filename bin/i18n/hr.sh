#!/bin/bash
# Croatian translations
#
# This file contains Croatian translations for bash scripts
# Format: declare -A TRANSLATIONS_HR=([key]='value' ...)

declare -A TRANSLATIONS_HR=(
    # Main messages
    ['loading_config']='Učitavanje konfiguracije...'
    ['checking_outdated']='Provjera zastarjelih paketa...'
    ['processing']='Obrada paketa...'
    ['processing_php']='Obrada paketa PHP skriptom...'
    ['running']='Pokretanje...'
    ['update_completed']='Ažuriranje dovršeno.'
    ['no_outdated']='Nema zastarjelih izravnih ovisnosti.'

    # Configuration
    ['found_config']='Pronađena konfiguracijska datoteka: '
    ['no_config']='Konfiguracijska datoteka nije pronađena (korištenje zadanih vrijednosti)'

    # Errors
    ['composer_not_found']='Composer nije instaliran ili nije u PATH-u.'
    ['composer_json_not_found']='composer.json nije pronađen u trenutnom direktoriju.'
    ['processor_not_found']='Nije moguće pronaći process-updates.php u vendor ili direktoriju skripti.'
    ['please_install']='Pokrenite: composer install'
    ['unknown_option']='Nepoznata opcija:'
    ['use_help']='Koristite --help ili -h za informacije o korištenju.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Trenutni direktorij:'
    ['debug_searching_config']='Pretraživanje konfiguracijskih datoteka:'
    ['debug_composer_executed']='Naredba composer outdated izvršena'
    ['debug_json_length']='Duljina OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated vratio je prazan JSON'
    ['debug_passing_to_php']='Prenošenje u PHP skriptu:'
    ['debug_output_length']='Duljina izlaza PHP skripte:'
    ['debug_processor_found']='PHP procesor pronađen na:'
)

