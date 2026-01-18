<?php
/**
 * Russian translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Нет пакетов для обновления',
    'all_up_to_date' => 'все пакеты актуальны',
    'all_have_conflicts' => 'все устаревшие пакеты имеют конфликты зависимостей',
    'all_ignored' => 'все устаревшие пакеты игнорируются',
    'all_ignored_or_conflicts' => 'все устаревшие пакеты игнорируются или имеют конфликты зависимостей',
    
    // Commands
    'suggested_commands' => 'Рекомендуемые команды:',
    'suggested_commands_conflicts' => 'Рекомендуемые команды для разрешения конфликтов зависимостей:',
    'includes_transitive' => '(Включает транзитивные зависимости, необходимые для разрешения конфликтов)',
    'update_transitive_first' => '(Сначала обновите эти транзитивные зависимости, затем повторите попытку обновления отфильтрованных пакетов)',
    
    // Framework and packages
    'detected_framework' => 'Обнаруженные ограничения фреймворка:',
    'ignored_packages_prod' => 'Игнорируемые пакеты (prod):',
    'ignored_packages_dev' => 'Игнорируемые пакеты (dev):',
    'dependency_analysis' => 'Анализ проверки зависимостей:',
    'all_outdated_before' => 'Все устаревшие пакеты (до проверки зависимостей):',
    'filtered_by_conflicts' => 'Отфильтровано по конфликтам зависимостей:',
    'suggested_transitive' => 'Рекомендуемые обновления транзитивных зависимостей для разрешения конфликтов:',
    'packages_passed_check' => 'Пакеты, прошедшие проверку зависимостей:',
    'none' => '(нет)',
    'conflicts_with' => 'конфликт с:',
    'package_abandoned' => 'Пакет заброшен',
    'replaced_by' => 'заменён на: %s',
    'alternative_solutions' => 'Альтернативные решения:',
    'compatible_with_conflicts' => 'совместим с конфликтующими зависимостями',
    'alternative_packages' => 'Альтернативные пакеты:',
    'recommended_replacement' => 'рекомендуемая замена',
    'similar_functionality' => 'похожая функциональность',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Total outdated packages: %d',
    'debug_require_packages' => 'require packages: %d',
    'debug_require_dev_packages' => 'require-dev packages: %d',
    'debug_detected_symfony' => 'Detected Symfony constraint: %s (from extra.symfony.require)',
    'debug_processing_package' => 'Processing package: %s (installed: %s, latest: %s)',
    'debug_action_ignored' => 'Action: IGNORED (in ignore list and not in include list)',
    'debug_action_skipped' => 'Action: SKIPPED (no compatible version found due to dependency constraints)',
    'debug_action_added' => 'Action: ADDED to %s dependencies: %s',
    'debug_no_compatible_version' => 'No compatible version found for %s (proposed: %s)',
    
    // Release info
    'release_info' => 'Информация о Версии',
    'release_changelog' => 'Журнал Изменений',
    'release_view_on_github' => 'Посмотреть на GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Проверка конфликтов зависимостей...',
    'checking_abandoned_packages' => '⏳ Проверка заброшенных пакетов...',
    'searching_fallback_versions' => '⏳ Поиск резервных версий...',
    'searching_alternative_packages' => '⏳ Поиск альтернативных пакетов...',
    'checking_maintainer_info' => '⏳ Проверка информации о сопровождающем...',
    
    // Impact analysis
    'impact_analysis' => 'Анализ влияния: Обновление {package} до {version} повлияет на:',
    'found_outdated_packages' => 'Найдено %d устаревших пакетов',
];

