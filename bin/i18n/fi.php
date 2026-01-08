<?php
/**
 * Finnish translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Ei paketteja päivitettäväksi',
    'all_up_to_date' => 'kaikki paketit ovat ajan tasalla',
    'all_have_conflicts' => 'kaikilla vanhentuneilla paketeilla on riippuvuuskonflikteja',
    'all_ignored' => 'kaikki vanhentuneet paketit ohitetaan',
    'all_ignored_or_conflicts' => 'kaikki vanhentuneet paketit ohitetaan tai niillä on riippuvuuskonflikteja',
    
    // Commands
    'suggested_commands' => 'Ehdotetut komennot:',
    'suggested_commands_conflicts' => 'Ehdotetut komennot riippuvuuskonfliktien ratkaisemiseksi:',
    'includes_transitive' => '(Sisältää transitiiviset riippuvuudet, joita tarvitaan konfliktien ratkaisemiseksi)',
    'update_transitive_first' => '(Päivitä ensin nämä transitiiviset riippuvuudet, yritä sitten päivittää suodatetut paketit)',
    
    // Framework and packages
    'detected_framework' => 'Havaitut kehysrajoitukset:',
    'ignored_packages_prod' => 'Ohitetut paketit (prod):',
    'ignored_packages_dev' => 'Ohitetut paketit (dev):',
    'dependency_analysis' => 'Riippuvuustarkistusanalyysi:',
    'all_outdated_before' => 'Kaikki vanhentuneet paketit (ennen riippuvuustarkistusta):',
    'filtered_by_conflicts' => 'Suodatettu riippuvuuskonfliktien mukaan:',
    'suggested_transitive' => 'Ehdotetut transitiivisten riippuvuuksien päivitykset konfliktien ratkaisemiseksi:',
    'packages_passed_check' => 'Paketit, jotka läpäisivät riippuvuustarkistuksen:',
    'none' => '(ei mitään)',
    'conflicts_with' => 'konfliktoi:',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Yhteensä vanhentuneita paketteja: %d',
    'debug_require_packages' => 'require paketit: %d',
    'debug_require_dev_packages' => 'require-dev paketit: %d',
    'debug_detected_symfony' => 'Havaittu Symfony-rajoitus: %s (osoitteesta extra.symfony.require)',
    'debug_processing_package' => 'Käsitellään pakettia: %s (asennettu: %s, uusin: %s)',
    'debug_action_ignored' => 'Toiminto: OHITETTU (ohituslistalla eikä sisällytyslistalla)',
    'debug_action_skipped' => 'Toiminto: OHITETTU (yhteensopivaa versiota ei löytynyt riippuvuusrajoitusten vuoksi)',
    'debug_action_added' => 'Toiminto: LISÄTTY %s riippuvuuksiin: %s',
    'debug_no_compatible_version' => 'Yhteensopivaa versiota ei löytynyt paketille %s (ehdotettu: %s)',
    
    // Release info
    'release_info' => 'Versiotiedot',
    'release_changelog' => 'Muutosloki',
    'release_view_on_github' => 'Näytä GitHubissa',
];

