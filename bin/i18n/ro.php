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
    'packages_passed_check' => 'Pachete care au trecut verificarea dependențelor:',
    'none' => '(niciunul)',
    'conflicts_with' => 'conflict cu:',
    
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
];

