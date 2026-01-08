<?php
/**
 * Croatian translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Nema paketa za ažuriranje',
    'all_up_to_date' => 'svi paketi su ažurni',
    'all_have_conflicts' => 'svi zastarjeli paketi imaju konflikte ovisnosti',
    'all_ignored' => 'svi zastarjeli paketi su ignorirani',
    'all_ignored_or_conflicts' => 'svi zastarjeli paketi su ignorirani ili imaju konflikte ovisnosti',
    
    // Commands
    'suggested_commands' => 'Predložene naredbe:',
    'suggested_commands_conflicts' => 'Predložene naredbe za rješavanje konflikata ovisnosti:',
    'includes_transitive' => '(Uključuje tranzitivne ovisnosti potrebne za rješavanje konflikata)',
    'update_transitive_first' => '(Prvo ažurirajte ove tranzitivne ovisnosti, zatim pokušajte ponovno ažurirati filtrirane pakete)',
    
    // Framework and packages
    'detected_framework' => 'Detektirana ograničenja okvira:',
    'ignored_packages_prod' => 'Ignorirani paketi (prod):',
    'ignored_packages_dev' => 'Ignorirani paketi (dev):',
    'dependency_analysis' => 'Analiza provjere ovisnosti:',
    'all_outdated_before' => 'Svi zastarjeli paketi (prije provjere ovisnosti):',
    'filtered_by_conflicts' => 'Filtrirano po konfliktima ovisnosti:',
    'suggested_transitive' => 'Predložena ažuriranja tranzitivnih ovisnosti za rješavanje konflikata:',
    'packages_passed_check' => 'Paketi koji su prošli provjeru ovisnosti:',
    'none' => '(nema)',
    'conflicts_with' => 'u konfliktu s:',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Ukupno zastarjelih paketa: %d',
    'debug_require_packages' => 'require paketi: %d',
    'debug_require_dev_packages' => 'require-dev paketi: %d',
    'debug_detected_symfony' => 'Detektirano ograničenje Symfony: %s (iz extra.symfony.require)',
    'debug_processing_package' => 'Obrada paketa: %s (instalirano: %s, najnovije: %s)',
    'debug_action_ignored' => 'Akcija: IGNORIRANO (na popisu ignoriranih i nije na popisu uključenih)',
    'debug_action_skipped' => 'Akcija: PRESKOČENO (kompatibilna verzija nije pronađena zbog ograničenja ovisnosti)',
    'debug_action_added' => 'Akcija: DODANO u %s ovisnosti: %s',
    'debug_no_compatible_version' => 'Kompatibilna verzija nije pronađena za %s (predloženo: %s)',
    
    // Release info
    'release_info' => 'Informacije o izdanju',
    'release_changelog' => 'Dnevnik promjena',
    'release_view_on_github' => 'Pogledaj na GitHubu',
];

