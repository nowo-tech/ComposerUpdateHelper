#!/bin/bash
# Russian translations
#
# This file contains Russian translations for bash scripts
# Format: declare -A TRANSLATIONS_RU=([key]='value' ...)

declare -A TRANSLATIONS_RU=(
    # Main messages
    ['loading_config']='Загрузка конфигурации...'
    ['checking_outdated']='Проверка устаревших пакетов...'
    ['processing']='Обработка пакетов...'
    ['processing_php']='Обработка пакетов с помощью скрипта PHP...'
    ['running']='Выполнение...'
    ['update_completed']='Обновление завершено.'
    ['no_outdated']='Нет устаревших прямых зависимостей.'

    # Configuration
    ['found_config']='Файл конфигурации найден: '
    ['no_config']='Файл конфигурации не найден (использование значений по умолчанию)'

    # Errors
    ['composer_not_found']='Composer не установлен или не находится в PATH.'
    ['composer_json_not_found']='composer.json не найден в текущей директории.'
    ['processor_not_found']='Не удалось найти process-updates.php в vendor или директории скриптов.'
    ['please_install']='Выполните: composer install'
    ['unknown_option']='Неизвестная опция:'
    ['use_help']='Используйте --help или -h для получения информации об использовании.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Текущая директория:'
    ['debug_searching_config']='Поиск файлов конфигурации:'
    ['debug_composer_executed']='Команда composer outdated выполнена'
    ['debug_json_length']='Длина OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated вернул пустой JSON'
    ['debug_passing_to_php']='Передача скрипту PHP:'
    ['debug_output_length']='Длина вывода скрипта PHP:'
    ['debug_processor_found']='Процессор PHP найден в:'
)

