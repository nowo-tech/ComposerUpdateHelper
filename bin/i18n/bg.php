<?php
/**
 * Bulgarian translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Няма пакети за актуализация',
    'all_up_to_date' => 'всички пакети са актуални',
    'all_have_conflicts' => 'всички остарели пакети имат конфликти на зависимости',
    'all_ignored' => 'всички остарели пакети се игнорират',
    'all_ignored_or_conflicts' => 'всички остарели пакети се игнорират или имат конфликти на зависимости',
    
    // Commands
    'suggested_commands' => 'Предложени команди:',
    'suggested_commands_conflicts' => 'Предложени команди за разрешаване на конфликти на зависимости:',
    'includes_transitive' => '(Включва транзитивни зависимости, необходими за разрешаване на конфликти)',
    'update_transitive_first' => '(Първо актуализирайте тези транзитивни зависимости, след това опитайте отново да актуализирате филтрираните пакети)',
    
    // Framework and packages
    'detected_framework' => 'Открити ограничения на рамката:',
    'ignored_packages_prod' => 'Игнорирани пакети (prod):',
    'ignored_packages_dev' => 'Игнорирани пакети (dev):',
    'dependency_analysis' => 'Анализ на проверка на зависимости:',
    'all_outdated_before' => 'Всички остарели пакети (преди проверка на зависимости):',
    'filtered_by_conflicts' => 'Филтрирани по конфликти на зависимости:',
    'suggested_transitive' => 'Предложени актуализации на транзитивни зависимости за разрешаване на конфликти:',
    'packages_passed_check' => 'Пакети, които преминаха проверката на зависимости:',
    'none' => '(няма)',
    'conflicts_with' => 'конфликтира с:',
    'package_abandoned' => 'Пакетът е изоставен',
    'replaced_by' => 'заменен с: %s',
    'alternative_solutions' => 'Алтернативни решения:',
    'compatible_with_conflicts' => 'съвместим с конфликтни зависимости',
    'alternative_packages' => 'Алтернативни пакети:',
    'recommended_replacement' => 'препоръчана замяна',
    'similar_functionality' => 'подобна функционалност',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Общо остарели пакети: %d',
    'debug_require_packages' => 'require пакети: %d',
    'debug_require_dev_packages' => 'require-dev пакети: %d',
    'debug_detected_symfony' => 'Открито ограничение на Symfony: %s (от extra.symfony.require)',
    'debug_processing_package' => 'Обработване на пакет: %s (инсталиран: %s, най-нов: %s)',
    'debug_action_ignored' => 'Действие: ИГНОРИРАНО (в списъка за игнориране и не в списъка за включване)',
    'debug_action_skipped' => 'Действие: ПРЕПРЪСНАТО (не е намерена съвместима версия поради ограничения на зависимости)',
    'debug_action_added' => 'Действие: ДОБАВЕНО към %s зависимости: %s',
    'debug_no_compatible_version' => 'Не е намерена съвместима версия за %s (предложено: %s)',
    
    // Release info
    'release_info' => 'Информация за изданието',
    'release_changelog' => 'Дневник на промените',
    'release_view_on_github' => 'Преглед в GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Проверка на конфликти на зависимости...',
    'checking_abandoned_packages' => '⏳ Проверка на изоставени пакети...',
    'searching_fallback_versions' => '⏳ Търсене на резервни версии...',
    'searching_alternative_packages' => '⏳ Търсене на алтернативни пакети...',
    'checking_maintainer_info' => '⏳ Проверка на информация за поддръжка...',
    
    // Impact analysis
    'impact_analysis' => 'Анализ на въздействието: Актуализирането на {package} до {version} ще засегне:',
    'found_outdated_packages' => 'Намерени %d остарели пакета',
];

