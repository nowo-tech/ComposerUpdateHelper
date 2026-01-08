<?php
/**
 * French translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Aucun paquet à mettre à jour',
    'all_up_to_date' => 'tous les paquets sont à jour',
    'all_have_conflicts' => 'tous les paquets obsolètes ont des conflits de dépendances',
    'all_ignored' => 'tous les paquets obsolètes sont ignorés',
    'all_ignored_or_conflicts' => 'tous les paquets obsolètes sont ignorés ou ont des conflits de dépendances',
    
    // Commands
    'suggested_commands' => 'Commandes suggérées:',
    'suggested_commands_conflicts' => 'Commandes suggérées pour résoudre les conflits de dépendances:',
    'includes_transitive' => '(Inclut les dépendances transitives nécessaires pour résoudre les conflits)',
    'update_transitive_first' => '(Mettez à jour ces dépendances transitives d\'abord, puis réessayez de mettre à jour les paquets filtrés)',
    
    // Framework and packages
    'detected_framework' => 'Contraintes du framework détectées:',
    'ignored_packages_prod' => 'Paquets ignorés (prod):',
    'ignored_packages_dev' => 'Paquets ignorés (dev):',
    'dependency_analysis' => 'Analyse de vérification des dépendances:',
    'all_outdated_before' => 'Tous les paquets obsolètes (avant la vérification des dépendances):',
    'filtered_by_conflicts' => 'Filtrés par conflits de dépendances:',
    'suggested_transitive' => 'Mises à jour de dépendances transitives suggérées pour résoudre les conflits:',
    'packages_passed_check' => 'Paquets qui ont passé la vérification des dépendances:',
    'none' => '(aucun)',
    'conflicts_with' => 'conflit avec:',
    
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
    'release_info' => 'Informations sur la Version',
    'release_changelog' => 'Journal des Modifications',
    'release_view_on_github' => 'Voir sur GitHub',
];

