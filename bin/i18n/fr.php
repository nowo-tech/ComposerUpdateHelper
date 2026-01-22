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
    'suggested_commands_grouped' => 'Commandes suggérées (essayez d\'installer ensemble - Composer peut mieux résoudre les conflits):',
    'grouped_install_explanation' => '(Installer plusieurs paquets ensemble aide parfois Composer à résoudre les conflits)',
    'grouped_install_warning' => '(Note: Cela peut encore échouer s\'il y a des conflits avec des paquets installés qui ne peuvent pas être mis à jour)',
    'copy_command_hint' => '(Select the command to copy)',
    'packages_need_maintainer_update' => 'Les paquets suivants ont besoin de mises à jour de leurs mainteneurs pour prendre en charge l\'installation groupée:',
    'package_needs_update_for_grouped' => '%s (installé: %s) a besoin d\'une mise à jour pour prendre en charge: %s (nécessite: %s)',
    'suggest_contact_maintainer' => '💡 Envisagez de contacter le mainteneur de %s pour demander le support de ces versions',
    'repository_url' => '📦 Dépôt: %s',
    'maintainers' => '👤 Mainteneurs: %s',
    'grouped_install_maintainer_needed' => 'Certains paquets installés ont besoin de mises à jour de leurs mainteneurs:',
    'package_needs_update' => '%s: Nécessite une mise à jour pour supporter %s (nécessite: %s)',
    'grouped_install_warning' => '(Note: This may still fail if there are conflicts with installed packages that cannot be updated)',
    'copy_command_hint' => '(Select the command to copy)',
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
    'no_compatible_dependent_versions' => 'Aucune version compatible de paquets dépendants trouvée:',
    'no_compatible_version_explanation' => '     - {depPackage}: Aucune version trouvée qui prend en charge {requiredBy}',
    'latest_checked_constraint' => '       (La dernière version vérifiée nécessite: {constraint})',
    'all_versions_require' => '       (Toutes les versions disponibles nécessitent: {constraint})',
    'packages_passed_check' => 'Paquets qui ont passé la vérification des dépendances:',
    'none' => '(aucun)',
    'conflicts_with' => 'conflit avec:',
    'package_abandoned' => 'Le paquet est abandonné',
    'abandoned_packages_section' => 'Paquets abandonnés trouvés:',
    'all_installed_abandoned_section' => 'Tous les paquets abandonnés installés:',
    'replaced_by' => 'remplacé par: %s',
    'alternative_solutions' => 'Solutions alternatives:',
    'compatible_with_conflicts' => 'compatible avec les dépendances en conflit',
    'alternative_packages' => 'Paquets alternatifs:',
    'recommended_replacement' => 'remplacement recommandé',
    'similar_functionality' => 'fonctionnalité similaire',
    
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
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Vérification des conflits de dépendances...',
    'checking_abandoned_packages' => '⏳ Vérification des paquets abandonnés...',
    'checking_all_abandoned_packages' => '⏳ Vérification de tous les paquets installés pour le statut abandonné...',
    'searching_fallback_versions' => '⏳ Recherche de versions de secours...',
    'searching_alternative_packages' => '⏳ Recherche de paquets alternatifs...',
    'checking_maintainer_info' => '⏳ Vérification des informations du mainteneur...',
    
    // Impact analysis
    'impact_analysis' => 'Analyse d\'impact: Mettre à jour {package} vers {version} affecterait:',
    'impact_analysis_saved' => '✅ Analyse d\'impact enregistrée dans: %s',
    'found_outdated_packages' => 'Trouvé %d paquet(s) obsolète(s)',
];

