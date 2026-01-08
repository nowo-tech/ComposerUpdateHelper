#!/bin/bash
# Chinese translations
#
# This file contains Chinese translations for bash scripts
# Format: declare -A TRANSLATIONS_ZH=([key]='value' ...)

declare -A TRANSLATIONS_ZH=(
    # Main messages
    ['loading_config']='正在加载配置...'
    ['checking_outdated']='正在检查过时的包...'
    ['processing']='正在处理包...'
    ['processing_php']='正在使用 PHP 脚本处理包...'
    ['running']='正在运行...'
    ['update_completed']='更新完成。'
    ['no_outdated']='没有过时的直接依赖。'

    # Configuration
    ['found_config']='找到配置文件: '
    ['no_config']='未找到配置文件 (使用默认值)'

    # Errors
    ['composer_not_found']='未安装 Composer 或不在 PATH 中。'
    ['composer_json_not_found']='在当前目录中未找到 composer.json。'
    ['processor_not_found']='在 vendor 或脚本目录中找不到 process-updates.php。'
    ['please_install']='请运行: composer install'
    ['unknown_option']='未知选项:'
    ['use_help']='使用 --help 或 -h 获取使用信息。'

    # Debug messages
    ['debug_prefix']='调试: '
    ['debug_current_dir']='当前目录:'
    ['debug_searching_config']='正在搜索配置文件:'
    ['debug_composer_executed']='已执行 composer outdated 命令'
    ['debug_json_length']='OUTDATED_JSON 长度:'
    ['debug_empty_json']='Composer outdated 返回空 JSON'
    ['debug_passing_to_php']='正在传递给 PHP 脚本:'
    ['debug_output_length']='PHP 脚本输出长度:'
    ['debug_processor_found']='在以下位置找到 PHP 处理器:'
)

