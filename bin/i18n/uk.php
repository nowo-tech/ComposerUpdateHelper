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
    'suggested_commands_grouped' => 'Рекомендовані команди (спробуйте встановити разом - Composer може краще вирішити конфлікти):',
    'grouped_install_explanation' => '(Встановлення кількох пакетів разом іноді допомагає Composer вирішити конфлікти)',
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
    'no_compatible_dependent_versions' => 'Сумісні версії залежних пакетів не знайдено:',
    'no_compatible_version_explanation' => '     - {depPackage}: Версію, що підтримує {requiredBy}, не знайдено',
    'latest_checked_constraint' => '       (Остання перевірена версія вимагає: {constraint})',
    'all_versions_require' => '       (Всі доступні версії вимагають: {constraint})',
    'packages_passed_check' => 'Пакети, які пройшли перевірку залежностей:',
    'none' => '(немає)',
    'conflicts_with' => 'конфліктує з:',
    'package_abandoned' => 'Пакет залишений',
    'abandoned_packages_section' => 'Знайдено занедбані пакети:',
    'all_installed_abandoned_section' => 'Всі встановлені занедбані пакети:',
    'replaced_by' => 'замінено на: %s',
    'alternative_solutions' => 'Альтернативні рішення:',
    'compatible_with_conflicts' => 'сумісний з конфліктними залежностями',
    'alternative_packages' => 'Альтернативні пакети:',
    'recommended_replacement' => 'рекомендована заміна',
    'similar_functionality' => 'схожа функціональність',
    
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
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Перевірка конфліктів залежностей...',
    'checking_abandoned_packages' => '⏳ Перевірка занедбаних пакетів...',
    'checking_all_abandoned_packages' => '⏳ Перевірка всіх встановлених пакетів на занедбаний статус...',
    'searching_fallback_versions' => '⏳ Пошук резервних версій...',
    'searching_alternative_packages' => '⏳ Пошук альтернативних пакетів...',
    'checking_maintainer_info' => '⏳ Перевірка інформації про супровідника...',
    
    // Impact analysis
    'impact_analysis' => 'Аналіз впливу: Оновлення {package} до {version} вплине на:',
    'impact_analysis_saved' => '✅ Аналіз впливу збережено в: %s',
    'found_outdated_packages' => 'Знайдено %d застарілих пакетів',
];

