<?php
/**
 * Italian translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Nessun pacchetto da aggiornare',
    'all_up_to_date' => 'tutti i pacchetti sono aggiornati',
    'all_have_conflicts' => 'tutti i pacchetti obsoleti hanno conflitti di dipendenze',
    'all_ignored' => 'tutti i pacchetti obsoleti sono ignorati',
    'all_ignored_or_conflicts' => 'tutti i pacchetti obsoleti sono ignorati o hanno conflitti di dipendenze',
    
    // Commands
    'suggested_commands' => 'Comandi suggeriti:',
    'suggested_commands_conflicts' => 'Comandi suggeriti per risolvere i conflitti di dipendenze:',
    'suggested_commands_grouped' => 'Comandi suggeriti (prova a installare insieme - Composer può risolvere meglio i conflitti):',
    'grouped_install_explanation' => '(Installare più pacchetti insieme a volte aiuta Composer a risolvere i conflitti)',
    'includes_transitive' => '(Include le dipendenze transitive necessarie per risolvere i conflitti)',
    'update_transitive_first' => '(Aggiorna prima queste dipendenze transitive, poi riprova ad aggiornare i pacchetti filtrati)',
    
    // Framework and packages
    'detected_framework' => 'Vincoli del framework rilevati:',
    'ignored_packages_prod' => 'Pacchetti ignorati (prod):',
    'ignored_packages_dev' => 'Pacchetti ignorati (dev):',
    'dependency_analysis' => 'Analisi della verifica delle dipendenze:',
    'all_outdated_before' => 'Tutti i pacchetti obsoleti (prima della verifica delle dipendenze):',
    'filtered_by_conflicts' => 'Filtrati per conflitti di dipendenze:',
    'suggested_transitive' => 'Aggiornamenti delle dipendenze transitive suggeriti per risolvere i conflitti:',
    'no_compatible_dependent_versions' => 'Nessuna versione compatibile di pacchetti dipendenti trovata:',
    'no_compatible_version_explanation' => '     - {depPackage}: Nessuna versione trovata che supporti {requiredBy}',
    'latest_checked_constraint' => '       (L\'ultima versione verificata richiede: {constraint})',
    'all_versions_require' => '       (Tutte le versioni disponibili richiedono: {constraint})',
    'packages_passed_check' => 'Pacchetti che hanno superato la verifica delle dipendenze:',
    'none' => '(nessuno)',
    'conflicts_with' => 'conflitto con:',
    'package_abandoned' => 'Il pacchetto è abbandonato',
    'abandoned_packages_section' => 'Pacchetti abbandonati trovati:',
    'all_installed_abandoned_section' => 'Tutti i pacchetti abbandonati installati:',
    'replaced_by' => 'sostituito da: %s',
    'alternative_solutions' => 'Soluzioni alternative:',
    'compatible_with_conflicts' => 'compatibile con dipendenze in conflitto',
    'alternative_packages' => 'Pacchetti alternativi:',
    'recommended_replacement' => 'sostituzione consigliata',
    'similar_functionality' => 'funzionalità simile',
    
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
    'release_info' => 'Informazioni sulla Versione',
    'release_changelog' => 'Registro delle Modifiche',
    'release_view_on_github' => 'Visualizza su GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Verifica dei conflitti di dipendenze...',
    'checking_abandoned_packages' => '⏳ Verifica dei pacchetti abbandonati...',
    'checking_all_abandoned_packages' => '⏳ Controllo di tutti i pacchetti installati per lo stato di abbandono...',
    'searching_fallback_versions' => '⏳ Ricerca di versioni fallback...',
    'searching_alternative_packages' => '⏳ Ricerca di pacchetti alternativi...',
    'checking_maintainer_info' => '⏳ Verifica delle informazioni del maintainer...',
    
    // Impact analysis
    'impact_analysis' => 'Analisi dell\'impatto: Aggiornare {package} a {version} influenzerebbe:',
    'impact_analysis_saved' => '✅ Analisi dell'impatto salvata in: %s',
    'found_outdated_packages' => 'Trovati %d pacchetto/i obsoleti',
];

