<?php
/**
 * Slovak translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Žiadne balíčky na aktualizáciu',
    'all_up_to_date' => 'všetky balíčky sú aktuálne',
    'all_have_conflicts' => 'všetky zastaralé balíčky majú konflikty závislostí',
    'all_ignored' => 'všetky zastaralé balíčky sú ignorované',
    'all_ignored_or_conflicts' => 'všetky zastaralé balíčky sú ignorované alebo majú konflikty závislostí',
    
    // Commands
    'suggested_commands' => 'Odporúčané príkazy:',
    'suggested_commands_conflicts' => 'Odporúčané príkazy na vyriešenie konfliktov závislostí:',
    'includes_transitive' => '(Zahŕňa tranzitívne závislosti potrebné na vyriešenie konfliktov)',
    'update_transitive_first' => '(Najprv aktualizujte tieto tranzitívne závislosti, potom skúste znova aktualizovať filtrované balíčky)',
    
    // Framework and packages
    'detected_framework' => 'Zistené obmedzenia frameworku:',
    'ignored_packages_prod' => 'Ignorované balíčky (prod):',
    'ignored_packages_dev' => 'Ignorované balíčky (dev):',
    'dependency_analysis' => 'Analýza kontroly závislostí:',
    'all_outdated_before' => 'Všetky zastaralé balíčky (pred kontrolou závislostí):',
    'filtered_by_conflicts' => 'Filtrované podľa konfliktov závislostí:',
    'suggested_transitive' => 'Odporúčané aktualizácie tranzitívnych závislostí na vyriešenie konfliktov:',
    'packages_passed_check' => 'Balíčky, ktoré prešli kontrolou závislostí:',
    'none' => '(žiadne)',
    'conflicts_with' => 'konfliktuje s:',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Celkom zastaralých balíčkov: %d',
    'debug_require_packages' => 'require balíčky: %d',
    'debug_require_dev_packages' => 'require-dev balíčky: %d',
    'debug_detected_symfony' => 'Zistené Symfony obmedzenie: %s (z extra.symfony.require)',
    'debug_processing_package' => 'Spracovanie balíčka: %s (nainštalované: %s, najnovšie: %s)',
    'debug_action_ignored' => 'Akcia: IGNOROVANÉ (v zozname ignorovaných a nie v zozname zahrnutých)',
    'debug_action_skipped' => 'Akcia: PRESKOČENÉ (nebola nájdená kompatibilná verzia kvôli obmedzeniam závislostí)',
    'debug_action_added' => 'Akcia: PRIDANÉ do %s závislostí: %s',
    'debug_no_compatible_version' => 'Nebola nájdená kompatibilná verzia pre %s (navrhnuté: %s)',
    
    // Release info
    'release_info' => 'Informácie o verzii',
    'release_changelog' => 'Zoznam zmien',
    'release_view_on_github' => 'Zobraziť na GitHub',
];

