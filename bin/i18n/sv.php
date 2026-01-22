<?php
/**
 * Swedish translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Inga paket att uppdatera',
    'all_up_to_date' => 'alla paket är uppdaterade',
    'all_have_conflicts' => 'alla föråldrade paket har beroendekonflikter',
    'all_ignored' => 'alla föråldrade paket ignoreras',
    'all_ignored_or_conflicts' => 'alla föråldrade paket ignoreras eller har beroendekonflikter',
    
    // Commands
    'suggested_commands' => 'Föreslagna kommandon:',
    'suggested_commands_conflicts' => 'Föreslagna kommandon för att lösa beroendekonflikter:',
    'suggested_commands_grouped' => 'Föreslagna kommandon (försök installera tillsammans - Composer kan lösa konflikter bättre):',
    'grouped_install_explanation' => '(Att installera flera paket tillsammans hjälper ibland Composer att lösa konflikter)',
    'includes_transitive' => '(Inkluderar transitiva beroenden som behövs för att lösa konflikter)',
    'update_transitive_first' => '(Uppdatera dessa transitiva beroenden först, försök sedan uppdatera de filtrerade paketen)',
    
    // Framework and packages
    'detected_framework' => 'Upptäckta ramverksbegränsningar:',
    'ignored_packages_prod' => 'Ignorerade paket (prod):',
    'ignored_packages_dev' => 'Ignorerade paket (dev):',
    'dependency_analysis' => 'Beroendekontrollanalys:',
    'all_outdated_before' => 'Alla föråldrade paket (före beroendekontroll):',
    'filtered_by_conflicts' => 'Filtrerade av beroendekonflikter:',
    'suggested_transitive' => 'Föreslagna uppdateringar av transitiva beroenden för att lösa konflikter:',
    'no_compatible_dependent_versions' => 'Inga kompatibla versioner av beroende paket hittades:',
    'no_compatible_version_explanation' => '     - {depPackage}: Ingen version hittades som stöder {requiredBy}',
    'latest_checked_constraint' => '       (Senaste kontrollerade versionen kräver: {constraint})',
    'all_versions_require' => '       (Alla tillgängliga versioner kräver: {constraint})',
    'packages_passed_check' => 'Paket som klarade beroendekontrollen:',
    'none' => '(inga)',
    'conflicts_with' => 'konfliktar med:',
    'package_abandoned' => 'Paketet är övergivet',
    'abandoned_packages_section' => 'Övergivna paket hittades:',
    'all_installed_abandoned_section' => 'Alla installerade övergivna paket:',
    'replaced_by' => 'ersatt av: %s',
    'alternative_solutions' => 'Alternativa lösningar:',
    'compatible_with_conflicts' => 'kompatibel med konflikterande beroenden',
    'alternative_packages' => 'Alternativa paket:',
    'recommended_replacement' => 'rekommenderad ersättning',
    'similar_functionality' => 'liknande funktionalitet',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Totalt föråldrade paket: %d',
    'debug_require_packages' => 'require paket: %d',
    'debug_require_dev_packages' => 'require-dev paket: %d',
    'debug_detected_symfony' => 'Upptäckt Symfony-begränsning: %s (från extra.symfony.require)',
    'debug_processing_package' => 'Bearbetar paket: %s (installerat: %s, senaste: %s)',
    'debug_action_ignored' => 'Åtgärd: IGNORERAD (i ignoreringslista och inte i inkluderingslista)',
    'debug_action_skipped' => 'Åtgärd: HOPPAD ÖVER (ingen kompatibel version hittades på grund av beroendebegränsningar)',
    'debug_action_added' => 'Åtgärd: TILLAGD till %s beroenden: %s',
    'debug_no_compatible_version' => 'Ingen kompatibel version hittades för %s (föreslagen: %s)',
    
    // Release info
    'release_info' => 'Versionsinformation',
    'release_changelog' => 'Ändringslogg',
    'release_view_on_github' => 'Visa på GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Kontrollerar beroendekonflikter...',
    'checking_abandoned_packages' => '⏳ Kontrollerar övergivna paket...',
    'checking_all_abandoned_packages' => '⏳ Kontrollerar alla installerade paket för övergiven status...',
    'searching_fallback_versions' => '⏳ Söker efter reservversioner...',
    'searching_alternative_packages' => '⏳ Söker efter alternativa paket...',
    'checking_maintainer_info' => '⏳ Kontrollerar maintainer-information...',
    
    // Impact analysis
    'impact_analysis' => 'Påverkan: Uppdatering av {package} till {version} skulle påverka:',
    'impact_analysis_saved' => '✅ Påverkan sparad i: %s',
    'found_outdated_packages' => 'Hittade %d föråldrade paket',
];

