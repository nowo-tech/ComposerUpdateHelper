#!/bin/bash
# Portuguese translations
#
# This file contains Portuguese translations for bash scripts
# Format: declare -A TRANSLATIONS_PT=([key]='value' ...)

declare -A TRANSLATIONS_PT=(
    # Main messages
    ['loading_config']='Carregando configuração...'
    ['checking_outdated']='Verificando pacotes desatualizados...'
    ['processing']='Processando pacotes...'
    ['processing_php']='Processando pacotes com script PHP...'
    ['running']='Executando...'
    ['update_completed']='Atualização concluída.'
    ['no_outdated']='Não há dependências diretas desatualizadas.'

    # Configuration
    ['found_config']='Arquivo de configuração encontrado: '
    ['no_config']='Nenhum arquivo de configuração encontrado (usando padrões)'

    # Errors
    ['composer_not_found']='Composer não está instalado ou não está no PATH.'
    ['composer_json_not_found']='composer.json não encontrado no diretório atual.'
    ['processor_not_found']='Não foi possível encontrar process-updates.php em vendor ou diretório de scripts.'
    ['please_install']='Execute: composer install'
    ['unknown_option']='Opção desconhecida:'
    ['use_help']='Use --help ou -h para informações de uso.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Diretório atual:'
    ['debug_searching_config']='Procurando arquivos de configuração:'
    ['debug_composer_executed']='Comando composer outdated executado'
    ['debug_json_length']='Comprimento de OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated retornou JSON vazio'
    ['debug_passing_to_php']='Passando para script PHP:'
    ['debug_output_length']='Comprimento da saída do script PHP:'
    ['debug_processor_found']='Processador PHP encontrado em:'
)

