<?php
/**
 * Dutch translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Geen pakketten om bij te werken',
    'all_up_to_date' => 'alle pakketten zijn up-to-date',
    'all_have_conflicts' => 'alle verouderde pakketten hebben afhankelijkheidsconflicten',
    'all_ignored' => 'alle verouderde pakketten worden genegeerd',
    'all_ignored_or_conflicts' => 'alle verouderde pakketten worden genegeerd of hebben afhankelijkheidsconflicten',
    
    // Commands
    'suggested_commands' => 'Voorgestelde commando\'s:',
    'suggested_commands_conflicts' => 'Voorgestelde commando\'s om afhankelijkheidsconflicten op te lossen:',
    'includes_transitive' => '(Inclusief transitieve afhankelijkheden die nodig zijn om conflicten op te lossen)',
    'update_transitive_first' => '(Werk eerst deze transitieve afhankelijkheden bij, probeer dan opnieuw de gefilterde pakketten bij te werken)',
    
    // Framework and packages
    'detected_framework' => 'Gedetecteerde framework beperkingen:',
    'ignored_packages_prod' => 'Genegeerde pakketten (prod):',
    'ignored_packages_dev' => 'Genegeerde pakketten (dev):',
    'dependency_analysis' => 'Afhankelijkheidscontrole analyse:',
    'all_outdated_before' => 'Alle verouderde pakketten (voor afhankelijkheidscontrole):',
    'filtered_by_conflicts' => 'Gefilterd door afhankelijkheidsconflicten:',
    'suggested_transitive' => 'Voorgestelde transitieve afhankelijkheidsupdates om conflicten op te lossen:',
    'packages_passed_check' => 'Pakketten die de afhankelijkheidscontrole hebben doorstaan:',
    'none' => '(geen)',
    'conflicts_with' => 'conflicteert met:',
    'package_abandoned' => 'Pakket is verlaten',
    'abandoned_packages_section' => 'Verlaten pakketten gevonden:',
    'all_installed_abandoned_section' => 'Alle geïnstalleerde verlaten pakketten:',
    'replaced_by' => 'vervangen door: %s',
    'alternative_solutions' => 'Alternatieve oplossingen:',
    'compatible_with_conflicts' => 'compatibel met conflicterende afhankelijkheden',
    'alternative_packages' => 'Alternatieve pakketten:',
    'recommended_replacement' => 'aanbevolen vervanging',
    'similar_functionality' => 'vergelijkbare functionaliteit',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Totaal verouderde pakketten: %d',
    'debug_require_packages' => 'require pakketten: %d',
    'debug_require_dev_packages' => 'require-dev pakketten: %d',
    'debug_detected_symfony' => 'Gedetecteerde Symfony beperking: %s (van extra.symfony.require)',
    'debug_processing_package' => 'Verwerken pakket: %s (geïnstalleerd: %s, nieuwste: %s)',
    'debug_action_ignored' => 'Actie: GENEGEERD (in negeerlijst en niet in opnamelijst)',
    'debug_action_skipped' => 'Actie: OVERGESLAGEN (geen compatibele versie gevonden vanwege afhankelijkheidsbeperkingen)',
    'debug_action_added' => 'Actie: TOEGEVOEGD aan %s afhankelijkheden: %s',
    'debug_no_compatible_version' => 'Geen compatibele versie gevonden voor %s (voorgesteld: %s)',
    
    // Release info
    'release_info' => 'Versie Informatie',
    'release_changelog' => 'Wijzigingslogboek',
    'release_view_on_github' => 'Bekijk op GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Controleren van afhankelijkheidsconflicten...',
    'checking_abandoned_packages' => '⏳ Controleren op verlaten pakketten...',
    'checking_all_abandoned_packages' => '⏳ Controleren van alle geïnstalleerde pakketten op verlaten status...',
    'searching_fallback_versions' => '⏳ Zoeken naar fallback-versies...',
    'searching_alternative_packages' => '⏳ Zoeken naar alternatieve pakketten...',
    'checking_maintainer_info' => '⏳ Controleren van maintainer-informatie...',
    
    // Impact analysis
    'impact_analysis' => 'Impactanalyse: Bijwerken van {package} naar {version} zou beïnvloeden:',
    'impact_analysis_saved' => '✅ Impactanalyse opgeslagen in: %s',
    'found_outdated_packages' => '%d verouderde pakket(ten) gevonden',
];

