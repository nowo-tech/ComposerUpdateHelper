<?php
/**
 * Danish translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Ingen pakker at opdatere',
    'all_up_to_date' => 'alle pakker er opdateret',
    'all_have_conflicts' => 'alle forældede pakker har afhængighedskonflikter',
    'all_ignored' => 'alle forældede pakker ignoreres',
    'all_ignored_or_conflicts' => 'alle forældede pakker ignoreres eller har afhængighedskonflikter',
    
    // Commands
    'suggested_commands' => 'Foreslåede kommandoer:',
    'suggested_commands_conflicts' => 'Foreslåede kommandoer til at løse afhængighedskonflikter:',
    'suggested_commands_grouped' => 'Foreslåede kommandoer (prøv at installere sammen - Composer kan måske løse konflikter bedre):',
    'grouped_install_explanation' => '(At installere flere pakker sammen hjælper nogle gange Composer med at løse konflikter)',
    'includes_transitive' => '(Inkluderer transitive afhængigheder, der er nødvendige for at løse konflikter)',
    'update_transitive_first' => '(Opdater først disse transitive afhængigheder, og prøv derefter igen at opdatere de filtrerede pakker)',
    
    // Framework and packages
    'detected_framework' => 'Registrerede framework-begrænsninger:',
    'ignored_packages_prod' => 'Ignorerede pakker (prod):',
    'ignored_packages_dev' => 'Ignorerede pakker (dev):',
    'dependency_analysis' => 'Afhængighedstjek-analyse:',
    'all_outdated_before' => 'Alle forældede pakker (før afhængighedstjek):',
    'filtered_by_conflicts' => 'Filtreret efter afhængighedskonflikter:',
    'suggested_transitive' => 'Foreslåede opdateringer af transitive afhængigheder til at løse konflikter:',
    'no_compatible_dependent_versions' => 'Ingen kompatible versioner af afhængige pakker fundet:',
    'no_compatible_version_explanation' => '     - {depPackage}: Ingen version fundet, der understøtter {requiredBy}',
    'latest_checked_constraint' => '       (Seneste kontrollerede version kræver: {constraint})',
    'all_versions_require' => '       (Alle tilgængelige versioner kræver: {constraint})',
    'packages_passed_check' => 'Pakker, der bestod afhængighedstjekket:',
    'none' => '(ingen)',
    'conflicts_with' => 'konflikt med:',
    'package_abandoned' => 'Pakken er forladt',
    'abandoned_packages_section' => 'Forladte pakker fundet:',
    'all_installed_abandoned_section' => 'Alle installerede forladte pakker:',
    'replaced_by' => 'erstattet af: %s',
    'alternative_solutions' => 'Alternative løsninger:',
    'compatible_with_conflicts' => 'kompatibel med konflikterende afhængigheder',
    'alternative_packages' => 'Alternative pakker:',
    'recommended_replacement' => 'anbefalet erstatning',
    'similar_functionality' => 'lignende funktionalitet',
    
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
    'release_info' => 'Versionsinformation',
    'release_changelog' => 'Ændringslog',
    'release_view_on_github' => 'Se på GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Tjekker afhængighedskonflikter...',
    'checking_abandoned_packages' => '⏳ Tjekker forladte pakker...',
    'checking_all_abandoned_packages' => '⏳ Tjekker alle installerede pakker for forladt status...',
    'searching_fallback_versions' => '⏳ Søger efter fallback-versioner...',
    'searching_alternative_packages' => '⏳ Søger efter alternative pakker...',
    'checking_maintainer_info' => '⏳ Tjekker maintainer-information...',
    
    // Impact analysis
    'impact_analysis' => 'Påvirkningsanalyse: Opdatering af {package} til {version} ville påvirke:',
    'impact_analysis_saved' => '✅ Påvirkningsanalyse gemt i: %s',
    'found_outdated_packages' => 'Fundet %d forældede pakker',
];

