<?php
/**
 * Ukrainian translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Немає пакетів для оновлення',
    'all_up_to_date' => 'всі пакети актуальні',
    'all_have_conflicts' => 'всі застарілі пакети мають конфлікти залежностей',
    'all_ignored' => 'всі застарілі пакети ігноруються',
    'all_ignored_or_conflicts' => 'всі застарілі пакети ігноруються або мають конфлікти залежностей',
    
    // Commands
    'suggested_commands' => 'Рекомендовані команди:',
    'suggested_commands_conflicts' => 'Рекомендовані команди для вирішення конфліктів залежностей:',
    'includes_transitive' => '(Включає транзитивні залежності, необхідні для вирішення конфліктів)',
    'update_transitive_first' => '(Спочатку оновіть ці транзитивні залежності, потім повторіть оновлення відфільтрованих пакетів)',
    
    // Framework and packages
    'detected_framework' => 'Виявлені обмеження фреймворку:',
    'ignored_packages_prod' => 'Ігноровані пакети (prod):',
    'ignored_packages_dev' => 'Ігноровані пакети (dev):',
    'dependency_analysis' => 'Аналіз перевірки залежностей:',
    'all_outdated_before' => 'Всі застарілі пакети (перед перевіркою залежностей):',
    'filtered_by_conflicts' => 'Відфільтровано за конфліктами залежностей:',
    'suggested_transitive' => 'Рекомендовані оновлення транзитивних залежностей для вирішення конфліктів:',
    'packages_passed_check' => 'Пакети, які пройшли перевірку залежностей:',
    'none' => '(немає)',
    'conflicts_with' => 'конфліктує з:',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Всього застарілих пакетів: %d',
    'debug_require_packages' => 'require пакети: %d',
    'debug_require_dev_packages' => 'require-dev пакети: %d',
    'debug_detected_symfony' => 'Виявлене обмеження Symfony: %s (з extra.symfony.require)',
    'debug_processing_package' => 'Обробка пакета: %s (встановлено: %s, останнє: %s)',
    'debug_action_ignored' => 'Дія: ІГНОРОВАНО (в списку ігнорування і не в списку включення)',
    'debug_action_skipped' => 'Дія: ПРОПУЩЕНО (сумісна версія не знайдена через обмеження залежностей)',
    'debug_action_added' => 'Дія: ДОДАНО до %s залежностей: %s',
    'debug_no_compatible_version' => 'Сумісна версія не знайдена для %s (запропоновано: %s)',
    
    // Release info
    'release_info' => 'Інформація про реліз',
    'release_changelog' => 'Журнал змін',
    'release_view_on_github' => 'Переглянути на GitHub',
];

