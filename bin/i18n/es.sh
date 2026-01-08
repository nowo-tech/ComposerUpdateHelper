#!/bin/bash
# Spanish translations
#
# This file contains Spanish translations for bash scripts
# Format: declare -A TRANSLATIONS_ES=([key]='value' ...)

declare -A TRANSLATIONS_ES=(
    # Main messages
    ['loading_config']='Cargando configuración...'
    ['checking_outdated']='Comprobando paquetes desactualizados...'
    ['processing']='Procesando paquetes...'
    ['processing_php']='Procesando paquetes con script PHP...'
    ['running']='Ejecutando...'
    ['update_completed']='Actualización completada.'
    ['no_outdated']='No hay dependencias directas desactualizadas.'

    # Configuration
    ['found_config']='Archivo de configuración encontrado: '
    ['no_config']='No se encontró archivo de configuración (usando valores por defecto)'

    # Errors
    ['composer_not_found']='Composer no está instalado o no está en PATH.'
    ['composer_json_not_found']='composer.json no encontrado en el directorio actual.'
    ['processor_not_found']='No se pudo encontrar process-updates.php en vendor o directorio de scripts.'
    ['please_install']='Ejecuta: composer install'
    ['unknown_option']='Opción desconocida:'
    ['use_help']='Usa --help o -h para información de uso.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Directorio actual:'
    ['debug_searching_config']='Buscando archivos de configuración:'
    ['debug_composer_executed']='Comando composer outdated ejecutado'
    ['debug_json_length']='Longitud de OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated devolvió JSON vacío'
    ['debug_passing_to_php']='Pasando a script PHP:'
    ['debug_output_length']='Longitud de salida del script PHP:'
    ['debug_processor_found']='Procesador PHP encontrado en:'
)

