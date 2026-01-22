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
    'suggested_commands_grouped' => 'Odporúčané príkazy (skúste nainštalovať spolu - Composer môže lepšie vyriešiť konflikty):',
    'grouped_install_explanation' => '(Inštalácia viacerých balíčkov spolu niekedy pomáha Composeru vyriešiť konflikty)',
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
    'no_compatible_dependent_versions' => 'Neboli nájdené kompatibilné verzie závislých balíčkov:',
    'no_compatible_version_explanation' => '     - {depPackage}: Nebola nájdená verzia, ktorá podporuje {requiredBy}',
    'latest_checked_constraint' => '       (Posledná skontrolovaná verzia vyžaduje: {constraint})',
    'all_versions_require' => '       (Všetky dostupné verzie vyžadujú: {constraint})',
    'packages_passed_check' => 'Balíčky, ktoré prešli kontrolou závislostí:',
    'none' => '(žiadne)',
    'conflicts_with' => 'konfliktuje s:',
    'package_abandoned' => 'Balíček je opustený',
    'abandoned_packages_section' => 'Nájdené opustené balíčky:',
    'all_installed_abandoned_section' => 'Všetky nainštalované opustené balíčky:',
    'replaced_by' => 'nahradený: %s',
    'alternative_solutions' => 'Alternatívne riešenia:',
    'compatible_with_conflicts' => 'kompatibilný s konfliktnými závislosťami',
    'alternative_packages' => 'Alternatívne balíčky:',
    'recommended_replacement' => 'odporúčaná náhrada',
    'similar_functionality' => 'podobná funkcionalita',
    
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
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Kontrola konfliktov závislostí...',
    'checking_abandoned_packages' => '⏳ Kontrola opustených balíčkov...',
    'checking_all_abandoned_packages' => '⏳ Kontrola všetkých nainštalovaných balíčkov na opustený stav...',
    'searching_fallback_versions' => '⏳ Vyhľadávanie záložných verzií...',
    'searching_alternative_packages' => '⏳ Vyhľadávanie alternatívnych balíčkov...',
    'checking_maintainer_info' => '⏳ Kontrola informácií o správcovi...',
    
    // Impact analysis
    'impact_analysis' => 'Analýza dopadu: Aktualizácia {package} na {version} by ovplyvnila:',
    'impact_analysis_saved' => '✅ Analýza dopadu uložená do: %s',
    'found_outdated_packages' => 'Nájdených %d zastaralých balíčkov',
];

