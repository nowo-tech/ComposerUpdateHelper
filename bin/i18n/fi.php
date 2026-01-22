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
    'suggested_commands_grouped' => 'Ehdotetut komennot (kokeile asentaa yhdessä - Composer voi ratkaista konfliktit paremmin):',
    'grouped_install_explanation' => '(Useiden pakettien asentaminen yhdessä auttaa joskus Composeria ratkaisemaan konfliktit)',
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
    'no_compatible_dependent_versions' => 'Yhteensopivia riippuvien pakettien versioita ei löytynyt:',
    'no_compatible_version_explanation' => '     - {depPackage}: Versiota, joka tukee {requiredBy}, ei löytynyt',
    'latest_checked_constraint' => '       (Viimeisin tarkistettu versio vaatii: {constraint})',
    'all_versions_require' => '       (Kaikki saatavilla olevat versiot vaativat: {constraint})',
    'packages_passed_check' => 'Paketit, jotka läpäisivät riippuvuustarkistuksen:',
    'none' => '(ei mitään)',
    'conflicts_with' => 'konfliktoi:',
    'package_abandoned' => 'Paketti on hylätty',
    'abandoned_packages_section' => 'Hylätyt paketit löydetty:',
    'all_installed_abandoned_section' => 'Kaikki asennetut hylätyt paketit:',
    'replaced_by' => 'korvattu: %s',
    'alternative_solutions' => 'Vaihtoehtoiset ratkaisut:',
    'compatible_with_conflicts' => 'yhteensopiva ristiriitaisten riippuvuuksien kanssa',
    'alternative_packages' => 'Vaihtoehtoiset paketit:',
    'recommended_replacement' => 'suositeltu korvaava',
    'similar_functionality' => 'samankaltainen toiminnallisuus',
    
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
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Tarkistetaan riippuvuuskonflikteja...',
    'checking_abandoned_packages' => '⏳ Tarkistetaan hylättyjä paketteja...',
    'checking_all_abandoned_packages' => '⏳ Tarkistetaan kaikki asennetut paketit hylättyä tilaa varten...',
    'searching_fallback_versions' => '⏳ Etsitään varaversioita...',
    'searching_alternative_packages' => '⏳ Etsitään vaihtoehtoisia paketteja...',
    'checking_maintainer_info' => '⏳ Tarkistetaan ylläpitäjätietoja...',
    
    // Impact analysis
    'impact_analysis' => 'Vaikutusanalyysi: {package} päivittäminen versioon {version} vaikuttaisi:',
    'impact_analysis_saved' => '✅ Vaikutusanalyysi tallennettu: %s',
    'found_outdated_packages' => 'Löydettiin %d vanhentunutta pakettia',
];

