<?php
/**
 * Norwegian translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Ingen pakker å oppdatere',
    'all_up_to_date' => 'alle pakker er oppdatert',
    'all_have_conflicts' => 'alle utdaterte pakker har avhengighetskonflikter',
    'all_ignored' => 'alle utdaterte pakker ignoreres',
    'all_ignored_or_conflicts' => 'alle utdaterte pakker ignoreres eller har avhengighetskonflikter',
    
    // Commands
    'suggested_commands' => 'Foreslåtte kommandoer:',
    'suggested_commands_conflicts' => 'Foreslåtte kommandoer for å løse avhengighetskonflikter:',
    'includes_transitive' => '(Inkluderer transitive avhengigheter som trengs for å løse konflikter)',
    'update_transitive_first' => '(Oppdater disse transitive avhengighetene først, prøv deretter å oppdatere de filtrerte pakkene)',
    
    // Framework and packages
    'detected_framework' => 'Oppdagede rammeverksbegrensninger:',
    'ignored_packages_prod' => 'Ignorerte pakker (prod):',
    'ignored_packages_dev' => 'Ignorerte pakker (dev):',
    'dependency_analysis' => 'Avhengighetskontrollanalyse:',
    'all_outdated_before' => 'Alle utdaterte pakker (før avhengighetskontroll):',
    'filtered_by_conflicts' => 'Filtrert av avhengighetskonflikter:',
    'suggested_transitive' => 'Foreslåtte oppdateringer av transitive avhengigheter for å løse konflikter:',
    'packages_passed_check' => 'Pakker som bestod avhengighetskontrollen:',
    'none' => '(ingen)',
    'conflicts_with' => 'konflikter med:',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Totalt utdaterte pakker: %d',
    'debug_require_packages' => 'require pakker: %d',
    'debug_require_dev_packages' => 'require-dev pakker: %d',
    'debug_detected_symfony' => 'Oppdaget Symfony-begrensning: %s (fra extra.symfony.require)',
    'debug_processing_package' => 'Behandler pakke: %s (installert: %s, siste: %s)',
    'debug_action_ignored' => 'Handling: IGNORERT (i ignoreringsliste og ikke i inkluderingsliste)',
    'debug_action_skipped' => 'Handling: HOPPET OVER (ingen kompatibel versjon funnet på grunn av avhengighetsbegrensninger)',
    'debug_action_added' => 'Handling: LAGT TIL til %s avhengigheter: %s',
    'debug_no_compatible_version' => 'Ingen kompatibel versjon funnet for %s (foreslått: %s)',
    
    // Release info
    'release_info' => 'Versjonsinformasjon',
    'release_changelog' => 'Endringslogg',
    'release_view_on_github' => 'Vis på GitHub',
];

