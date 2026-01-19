<?php
/**
 * Japanese translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => '更新するパッケージがありません',
    'all_up_to_date' => 'すべてのパッケージは最新です',
    'all_have_conflicts' => 'すべての古いパッケージに依存関係の競合があります',
    'all_ignored' => 'すべての古いパッケージは無視されています',
    'all_ignored_or_conflicts' => 'すべての古いパッケージは無視されているか、依存関係の競合があります',
    
    // Commands
    'suggested_commands' => '推奨コマンド:',
    'suggested_commands_conflicts' => '依存関係の競合を解決するための推奨コマンド:',
    'includes_transitive' => '(競合を解決するために必要な推移的依存関係を含む)',
    'update_transitive_first' => '(まずこれらの推移的依存関係を更新し、次にフィルタリングされたパッケージの更新を再試行してください)',
    
    // Framework and packages
    'detected_framework' => '検出されたフレームワーク制約:',
    'ignored_packages_prod' => '無視されたパッケージ (prod):',
    'ignored_packages_dev' => '無視されたパッケージ (dev):',
    'dependency_analysis' => '依存関係チェック分析:',
    'all_outdated_before' => 'すべての古いパッケージ (依存関係チェック前):',
    'filtered_by_conflicts' => '依存関係の競合でフィルタリング:',
    'suggested_transitive' => '競合を解決するための推奨推移的依存関係更新:',
    'packages_passed_check' => '依存関係チェックを通過したパッケージ:',
    'none' => '(なし)',
    'conflicts_with' => 'と競合:',
    'package_abandoned' => 'パッケージは放棄されました',
    'abandoned_packages_section' => '廃止されたパッケージが見つかりました:',
    'all_installed_abandoned_section' => 'インストールされているすべての廃止されたパッケージ:',
    'replaced_by' => '置き換え: %s',
    'alternative_solutions' => '代替ソリューション:',
    'compatible_with_conflicts' => '競合する依存関係と互換性あり',
    'alternative_packages' => '代替パッケージ:',
    'recommended_replacement' => '推奨される置き換え',
    'similar_functionality' => '類似機能',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => '古いパッケージの合計: %d',
    'debug_require_packages' => 'require パッケージ: %d',
    'debug_require_dev_packages' => 'require-dev パッケージ: %d',
    'debug_detected_symfony' => '検出された Symfony 制約: %s (extra.symfony.require から)',
    'debug_processing_package' => 'パッケージを処理中: %s (インストール済み: %s, 最新: %s)',
    'debug_action_ignored' => 'アクション: 無視されました (無視リストにあり、含めるリストにありません)',
    'debug_action_skipped' => 'アクション: スキップされました (依存関係制約により互換性のあるバージョンが見つかりませんでした)',
    'debug_action_added' => 'アクション: %s 依存関係に追加されました: %s',
    'debug_no_compatible_version' => '%s の互換性のあるバージョンが見つかりませんでした (提案: %s)',
    
    // Release info
    'release_info' => 'リリース情報',
    'release_changelog' => '変更ログ',
    'release_view_on_github' => 'GitHub で表示',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ 依存関係の競合を確認中...',
    'checking_abandoned_packages' => '⏳ 廃止されたパッケージを確認中...',
    'checking_all_abandoned_packages' => '⏳ インストールされているすべてのパッケージの廃止状態を確認中...',
    'searching_fallback_versions' => '⏳ フォールバックバージョンを検索中...',
    'searching_alternative_packages' => '⏳ 代替パッケージを検索中...',
    'checking_maintainer_info' => '⏳ メンテナ情報を確認中...',
    
    // Impact analysis
    'impact_analysis' => '影響分析：{package} を {version} に更新すると、以下に影響します：',
    'impact_analysis_saved' => '✅ 影響分析を保存しました: %s',
    'found_outdated_packages' => '%d 個の古いパッケージが見つかりました',
];

