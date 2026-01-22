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
    'suggested_commands_grouped' => '建议的命令 (尝试一起安装 - Composer 可能更好地解决冲突):',
    'grouped_install_explanation' => '(一起安装多个包有时有助于 Composer 解决冲突)',
    'grouped_install_warning' => '(注意: 如果与无法更新的已安装包存在冲突，这可能仍然会失败)',
    'copy_command_hint' => '(Click to copy or select the command)',
    'packages_need_maintainer_update' => '(The following packages need updates from their maintainers)',
    'package_needs_update_for_grouped' => '%s (installed: %s) needs update to support: %s (requires: %s)',
    'suggest_contact_maintainer' => '💡 Consider contacting the maintainer of %s',
    'repository_url' => '📦 Repository: %s',
    'maintainers' => '👤 Maintainers: %s',
    'grouped_install_maintainer_needed' => '某些已安装的包需要其maintainer的更新:',
    'package_needs_update' => '%s: 需要更新以支持 %s (需要: %s)',
    'grouped_install_warning' => '(Note: This may still fail if there are conflicts with installed packages that cannot be updated)',
    'copy_command_hint' => '(Click to copy or select the command)',
    'packages_need_maintainer_update' => '(The following packages need updates from their maintainers)',
    'package_needs_update_for_grouped' => '%s (installed: %s) needs update to support: %s (requires: %s)',
    'suggest_contact_maintainer' => '💡 Consider contacting the maintainer of %s',
    'repository_url' => '📦 Repository: %s',
    'maintainers' => '👤 Maintainers: %s',
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
    'no_compatible_dependent_versions' => '未找到兼容的依赖包版本:',
    'no_compatible_version_explanation' => '     - {depPackage}: 未找到支持 {requiredBy} 的版本',
    'latest_checked_constraint' => '       (最新检查的版本需要: {constraint})',
    'all_versions_require' => '       (所有可用版本都需要: {constraint})',
    'packages_passed_check' => '通过依赖检查的包:',
    'none' => '(无)',
    'conflicts_with' => '与以下冲突:',
    'package_abandoned' => '包已弃用',
    'abandoned_packages_section' => '发现已弃用的包:',
    'all_installed_abandoned_section' => '所有已安装的已弃用包:',
    'replaced_by' => '替换为: %s',
    'alternative_solutions' => '替代方案:',
    'compatible_with_conflicts' => '与冲突依赖项兼容',
    'alternative_packages' => '替代包:',
    'recommended_replacement' => '推荐的替代',
    'similar_functionality' => '类似功能',
    
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
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ 检查依赖冲突...',
    'checking_abandoned_packages' => '⏳ 检查已弃用的包...',
    'checking_all_abandoned_packages' => '⏳ 检查所有已安装的包是否已弃用...',
    'searching_fallback_versions' => '⏳ 搜索回退版本...',
    'searching_alternative_packages' => '⏳ 搜索替代包...',
    'checking_maintainer_info' => '⏳ 检查维护者信息...',
    
    // Impact analysis
    'impact_analysis' => '影响分析：将 {package} 更新到 {version} 会影响：',
    'impact_analysis_saved' => '✅ 影响分析已保存到: %s',
    'found_outdated_packages' => '找到 %d 个过时的包',
];

