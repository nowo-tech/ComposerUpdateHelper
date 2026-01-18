<?php
/**
 * German translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Keine Pakete zum Aktualisieren',
    'all_up_to_date' => 'alle Pakete sind auf dem neuesten Stand',
    'all_have_conflicts' => 'alle veralteten Pakete haben Abhängigkeitskonflikte',
    'all_ignored' => 'alle veralteten Pakete werden ignoriert',
    'all_ignored_or_conflicts' => 'alle veralteten Pakete werden ignoriert oder haben Abhängigkeitskonflikte',
    
    // Commands
    'suggested_commands' => 'Vorgeschlagene Befehle:',
    'suggested_commands_conflicts' => 'Vorgeschlagene Befehle zur Behebung von Abhängigkeitskonflikten:',
    'includes_transitive' => '(Enthält transitive Abhängigkeiten, die zur Behebung von Konflikten erforderlich sind)',
    'update_transitive_first' => '(Aktualisieren Sie zuerst diese transitiven Abhängigkeiten, dann versuchen Sie erneut, die gefilterten Pakete zu aktualisieren)',
    
    // Framework and packages
    'detected_framework' => 'Erkannte Framework-Einschränkungen:',
    'ignored_packages_prod' => 'Ignorierte Pakete (prod):',
    'ignored_packages_dev' => 'Ignorierte Pakete (dev):',
    'dependency_analysis' => 'Abhängigkeitsprüfungsanalyse:',
    'all_outdated_before' => 'Alle veralteten Pakete (vor der Abhängigkeitsprüfung):',
    'filtered_by_conflicts' => 'Gefiltert nach Abhängigkeitskonflikten:',
    'suggested_transitive' => 'Vorgeschlagene Updates transitiver Abhängigkeiten zur Behebung von Konflikten:',
    'packages_passed_check' => 'Pakete, die die Abhängigkeitsprüfung bestanden haben:',
    'none' => '(keine)',
    'conflicts_with' => 'Konflikt mit:',
    'package_abandoned' => 'Paket ist eingestellt',
    'replaced_by' => 'ersetzt durch: %s',
    'alternative_solutions' => 'Alternative Lösungen:',
    'compatible_with_conflicts' => 'kompatibel mit widersprüchlichen Abhängigkeiten',
    'alternative_packages' => 'Alternative Pakete:',
    'recommended_replacement' => 'empfohlene Ersetzung',
    'similar_functionality' => 'ähnliche Funktionalität',
    
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
    'release_info' => 'Versionsinformationen',
    'release_changelog' => 'Änderungsprotokoll',
    'release_view_on_github' => 'Auf GitHub anzeigen',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Überprüfung von Abhängigkeitskonflikten...',
    'checking_abandoned_packages' => '⏳ Überprüfung auf eingestellte Pakete...',
    'searching_fallback_versions' => '⏳ Suche nach Fallback-Versionen...',
    'searching_alternative_packages' => '⏳ Suche nach alternativen Paketen...',
    'checking_maintainer_info' => '⏳ Überprüfung der Maintainer-Informationen...',
    
    // Impact analysis
    'impact_analysis' => 'Auswirkungsanalyse: Aktualisierung von {package} auf {version} würde beeinflussen:',
    'found_outdated_packages' => '%d veraltete Paket(e) gefunden',
];

