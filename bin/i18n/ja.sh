#!/bin/bash
# Japanese translations
#
# This file contains Japanese translations for bash scripts
# Format: declare -A TRANSLATIONS_JA=([key]='value' ...)

declare -A TRANSLATIONS_JA=(
    # Main messages
    ['loading_config']='設定を読み込み中...'
    ['checking_outdated']='古いパッケージを確認中...'
    ['processing']='パッケージを処理中...'
    ['processing_php']='PHP スクリプトでパッケージを処理中...'
    ['running']='実行中...'
    ['update_completed']='更新が完了しました。'
    ['no_outdated']='古い直接依存関係はありません。'

    # Configuration
    ['found_config']='設定ファイルが見つかりました: '
    ['no_config']='設定ファイルが見つかりませんでした (デフォルト値を使用)'

    # Errors
    ['composer_not_found']='Composer がインストールされていないか、PATH にありません。'
    ['composer_json_not_found']='現在のディレクトリに composer.json が見つかりませんでした。'
    ['processor_not_found']='vendor またはスクリプトディレクトリに process-updates.php が見つかりませんでした。'
    ['please_install']='実行してください: composer install'
    ['unknown_option']='不明なオプション:'
    ['use_help']='使用方法については --help または -h を使用してください。'

    # Debug messages
    ['debug_prefix']='デバッグ: '
    ['debug_current_dir']='現在のディレクトリ:'
    ['debug_searching_config']='設定ファイルを検索中:'
    ['debug_composer_executed']='composer outdated コマンドが実行されました'
    ['debug_json_length']='OUTDATED_JSON の長さ:'
    ['debug_empty_json']='Composer outdated が空の JSON を返しました'
    ['debug_passing_to_php']='PHP スクリプトに渡しています:'
    ['debug_output_length']='PHP スクリプトの出力の長さ:'
    ['debug_processor_found']='PHP プロセッサが見つかりました:'
)

