#!/bin/bash
# Ukrainian translations
#
# This file contains Ukrainian translations for bash scripts
# Format: declare -A TRANSLATIONS_UK=([key]='value' ...)

declare -A TRANSLATIONS_UK=(
    # Main messages
    ['loading_config']='Завантаження конфігурації...'
    ['checking_outdated']='Перевірка застарілих пакетів...'
    ['processing']='Обробка пакетів...'
    ['processing_php']='Обробка пакетів за допомогою PHP скрипта...'
    ['running']='Виконання...'
    ['update_completed']='Оновлення завершено.'
    ['no_outdated']='Немає застарілих прямих залежностей.'

    # Configuration
    ['found_config']='Знайдено файл конфігурації: '
    ['no_config']='Файл конфігурації не знайдено (використання значень за замовчуванням)'

    # Errors
    ['composer_not_found']='Composer не встановлено або не в PATH.'
    ['composer_json_not_found']='composer.json не знайдено в поточній директорії.'
    ['processor_not_found']='Не вдалося знайти process-updates.php у vendor або директорії скриптів.'
    ['please_install']='Запустіть: composer install'
    ['unknown_option']='Невідома опція:'
    ['use_help']='Використовуйте --help або -h для інформації про використання.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Поточна директорія:'
    ['debug_searching_config']='Пошук файлів конфігурації:'
    ['debug_composer_executed']='Команда composer outdated виконана'
    ['debug_json_length']='Довжина OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated повернув порожній JSON'
    ['debug_passing_to_php']='Передача до PHP скрипта:'
    ['debug_output_length']='Довжина виводу PHP скрипта:'
    ['debug_processor_found']='PHP процесор знайдено на:'
)

