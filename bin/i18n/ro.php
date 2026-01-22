<?php
/**
 * Romanian translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Niciun pachet de actualizat',
    'all_up_to_date' => 'toate pachetele sunt actualizate',
    'all_have_conflicts' => 'toate pachetele învechite au conflicte de dependențe',
    'all_ignored' => 'toate pachetele învechite sunt ignorate',
    'all_ignored_or_conflicts' => 'toate pachetele învechite sunt ignorate sau au conflicte de dependențe',
    
    // Commands
    'suggested_commands' => 'Comenzi sugerate:',
    'suggested_commands_conflicts' => 'Comenzi sugerate pentru rezolvarea conflictelor de dependențe:',
    'suggested_commands_grouped' => 'Comenzi sugerate (încercați să instalați împreună - Composer poate rezolva mai bine conflictele):',
    'grouped_install_explanation' => '(Instalarea mai multor pachete împreună ajută uneori Composer să rezolve conflictele)',
    'includes_transitive' => '(Include dependențele transitive necesare pentru rezolvarea conflictelor)',
    'update_transitive_first' => '(Actualizați mai întâi aceste dependențe transitive, apoi încercați din nou să actualizați pachetele filtrate)',
    
    // Framework and packages
    'detected_framework' => 'Constrângeri de framework detectate:',
    'ignored_packages_prod' => 'Pachete ignorate (prod):',
    'ignored_packages_dev' => 'Pachete ignorate (dev):',
    'dependency_analysis' => 'Analiză de verificare a dependențelor:',
    'all_outdated_before' => 'Toate pachetele învechite (înainte de verificarea dependențelor):',
    'filtered_by_conflicts' => 'Filtrate după conflicte de dependențe:',
    'suggested_transitive' => 'Actualizări de dependențe transitive sugerate pentru rezolvarea conflictelor:',
    'no_compatible_dependent_versions' => 'Nu au fost găsite versiuni compatibile ale pachetelor dependente:',
    'no_compatible_version_explanation' => '     - {depPackage}: Nu a fost găsită nicio versiune care să suporte {requiredBy}',
    'latest_checked_constraint' => '       (Ultima versiune verificată necesită: {constraint})',
    'all_versions_require' => '       (Toate versiunile disponibile necesită: {constraint})',
    'packages_passed_check' => 'Pachete care au trecut verificarea dependențelor:',
    'none' => '(niciunul)',
    'conflicts_with' => 'conflict cu:',
    'package_abandoned' => 'Pachetul este abandonat',
    'abandoned_packages_section' => 'Pachete abandonate găsite:',
    'all_installed_abandoned_section' => 'Toate pachetele abandonate instalate:',
    'replaced_by' => 'înlocuit cu: %s',
    'alternative_solutions' => 'Soluții alternative:',
    'compatible_with_conflicts' => 'compatibil cu dependențe conflictuale',
    'alternative_packages' => 'Pachete alternative:',
    'recommended_replacement' => 'înlocuire recomandată',
    'similar_functionality' => 'funcționalitate similară',
    
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
    'release_info' => 'Informații despre Versiune',
    'release_changelog' => 'Jurnalul Modificărilor',
    'release_view_on_github' => 'Vezi pe GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Verificarea conflictelor de dependențe...',
    'checking_abandoned_packages' => '⏳ Verificarea pachetelor abandonate...',
    'checking_all_abandoned_packages' => '⏳ Verificarea tuturor pachetelor instalate pentru status abandonat...',
    'searching_fallback_versions' => '⏳ Căutarea versiunilor de rezervă...',
    'searching_alternative_packages' => '⏳ Căutarea pachetelor alternative...',
    'checking_maintainer_info' => '⏳ Verificarea informațiilor despre mentenor...',
    
    // Impact analysis
    'impact_analysis' => 'Analiza impactului: Actualizarea {package} la {version} ar afecta:',
    'impact_analysis_saved' => '✅ Analiza impactului salvată în: %s',
    'found_outdated_packages' => 'Găsite %d pachet(e) învechite',
];

