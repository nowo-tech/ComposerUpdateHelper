#!/bin/bash
# Bulgarian translations
#
# This file contains Bulgarian translations for bash scripts
# Format: declare -A TRANSLATIONS_BG=([key]='value' ...)

declare -A TRANSLATIONS_BG=(
    # Main messages
    ['loading_config']='Зареждане на конфигурация...'
    ['checking_outdated']='Проверка на остарели пакети...'
    ['processing']='Обработване на пакети...'
    ['processing_php']='Обработване на пакети с PHP скрипт...'
    ['running']='Изпълнение...'
    ['update_completed']='Актуализацията завършена.'
    ['no_outdated']='Няма остарели директни зависимости.'

    # Configuration
    ['found_config']='Намерен конфигурационен файл: '
    ['no_config']='Конфигурационен файл не е намерен (използване на стойности по подразбиране)'

    # Errors
    ['composer_not_found']='Composer не е инсталиран или не е в PATH.'
    ['composer_json_not_found']='composer.json не е намерен в текущата директория.'
    ['processor_not_found']='Не може да се намери process-updates.php в vendor или директорията на скриптовете.'
    ['please_install']='Изпълнете: composer install'
    ['unknown_option']='Неизвестна опция:'
    ['use_help']='Използвайте --help или -h за информация за използване.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Текуща директория:'
    ['debug_searching_config']='Търсене на конфигурационни файлове:'
    ['debug_composer_executed']='Команда composer outdated изпълнена'
    ['debug_json_length']='Дължина на OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated върна празен JSON'
    ['debug_passing_to_php']='Предаване към PHP скрипт:'
    ['debug_output_length']='Дължина на изхода на PHP скрипта:'
    ['debug_processor_found']='PHP процесор намерен на:'
)

