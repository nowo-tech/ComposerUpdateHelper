<?php
/**
 * Chinese translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => '没有要更新的包',
    'all_up_to_date' => '所有包都是最新的',
    'all_have_conflicts' => '所有过时的包都有依赖冲突',
    'all_ignored' => '所有过时的包都被忽略',
    'all_ignored_or_conflicts' => '所有过时的包都被忽略或有依赖冲突',
    
    // Commands
    'suggested_commands' => '建议的命令:',
    'suggested_commands_conflicts' => '解决依赖冲突的建议命令:',
    'includes_transitive' => '(包括解决冲突所需的传递依赖)',
    'update_transitive_first' => '(首先更新这些传递依赖，然后重试更新过滤的包)',
    
    // Framework and packages
    'detected_framework' => '检测到的框架约束:',
    'ignored_packages_prod' => '忽略的包 (prod):',
    'ignored_packages_dev' => '忽略的包 (dev):',
    'dependency_analysis' => '依赖检查分析:',
    'all_outdated_before' => '所有过时的包 (依赖检查之前):',
    'filtered_by_conflicts' => '按依赖冲突过滤:',
    'suggested_transitive' => '建议的传递依赖更新以解决冲突:',
    'packages_passed_check' => '通过依赖检查的包:',
    'none' => '(无)',
    'conflicts_with' => '与以下冲突:',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => '过时包总数: %d',
    'debug_require_packages' => 'require 包: %d',
    'debug_require_dev_packages' => 'require-dev 包: %d',
    'debug_detected_symfony' => '检测到的 Symfony 约束: %s (来自 extra.symfony.require)',
    'debug_processing_package' => '处理包: %s (已安装: %s, 最新: %s)',
    'debug_action_ignored' => '操作: 已忽略 (在忽略列表中且不在包含列表中)',
    'debug_action_skipped' => '操作: 已跳过 (由于依赖约束未找到兼容版本)',
    'debug_action_added' => '操作: 已添加到 %s 依赖: %s',
    'debug_no_compatible_version' => '未找到 %s 的兼容版本 (建议: %s)',
    
    // Release info
    'release_info' => '版本信息',
    'release_changelog' => '更新日志',
    'release_view_on_github' => '在 GitHub 上查看',
];

