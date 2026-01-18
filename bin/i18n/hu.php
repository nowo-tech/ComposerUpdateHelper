<?php
/**
 * Hungarian translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Nincs frissítendő csomag',
    'all_up_to_date' => 'minden csomag naprakész',
    'all_have_conflicts' => 'minden elavult csomagnak függőségi konfliktusa van',
    'all_ignored' => 'minden elavult csomag figyelmen kívül van hagyva',
    'all_ignored_or_conflicts' => 'minden elavult csomag figyelmen kívül van hagyva vagy függőségi konfliktusa van',
    
    // Commands
    'suggested_commands' => 'Javasolt parancsok:',
    'suggested_commands_conflicts' => 'Javasolt parancsok a függőségi konfliktusok megoldásához:',
    'includes_transitive' => '(Tartalmazza a konfliktusok megoldásához szükséges tranzitív függőségeket)',
    'update_transitive_first' => '(Először frissítse ezeket a tranzitív függőségeket, majd próbálja újra frissíteni a szűrt csomagokat)',
    
    // Framework and packages
    'detected_framework' => 'Észlelt keretrendszer korlátozások:',
    'ignored_packages_prod' => 'Figyelmen kívül hagyott csomagok (prod):',
    'ignored_packages_dev' => 'Figyelmen kívül hagyott csomagok (dev):',
    'dependency_analysis' => 'Függőség ellenőrzési elemzés:',
    'all_outdated_before' => 'Minden elavult csomag (függőség ellenőrzés előtt):',
    'filtered_by_conflicts' => 'Függőségi konfliktusok szerint szűrve:',
    'suggested_transitive' => 'Javasolt tranzitív függőség frissítések a konfliktusok megoldásához:',
    'packages_passed_check' => 'A függőség ellenőrzést átlépő csomagok:',
    'none' => '(nincs)',
    'conflicts_with' => 'konfliktus:',
    'package_abandoned' => 'A csomag elhagyott',
    'replaced_by' => 'lecserélve: %s',
    'alternative_solutions' => 'Alternatív megoldások:',
    'compatible_with_conflicts' => 'kompatibilis konfliktusos függőségekkel',
    'alternative_packages' => 'Alternatív csomagok:',
    'recommended_replacement' => 'ajánlott csere',
    'similar_functionality' => 'hasonló funkcionalitás',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Összes elavult csomag: %d',
    'debug_require_packages' => 'require csomagok: %d',
    'debug_require_dev_packages' => 'require-dev csomagok: %d',
    'debug_detected_symfony' => 'Észlelt Symfony korlátozás: %s (extra.symfony.require-ből)',
    'debug_processing_package' => 'Csomag feldolgozása: %s (telepítve: %s, legújabb: %s)',
    'debug_action_ignored' => 'Művelet: FIGYELMEN KÍVÜL HAGYVA (a figyelmen kívül hagyott listában van és nincs a tartalmazott listában)',
    'debug_action_skipped' => 'Művelet: KIHAGYVA (nincs kompatibilis verzió a függőségi korlátozások miatt)',
    'debug_action_added' => 'Művelet: HOZZÁADVA a %s függőségekhez: %s',
    'debug_no_compatible_version' => 'Nem található kompatibilis verzió a %s számára (javasolt: %s)',
    
    // Release info
    'release_info' => 'Kiadás Információ',
    'release_changelog' => 'Változásnapló',
    'release_view_on_github' => 'Megtekintés a GitHubon',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Függőségi konfliktusok ellenőrzése...',
    'checking_abandoned_packages' => '⏳ Elhagyott csomagok ellenőrzése...',
    'searching_fallback_versions' => '⏳ Tartalék verziók keresése...',
    'searching_alternative_packages' => '⏳ Alternatív csomagok keresése...',
    'checking_maintainer_info' => '⏳ Karbantartói információk ellenőrzése...',
    
    // Impact analysis
    'impact_analysis' => 'Hatáselemzés: A {package} frissítése {version} verzióra befolyásolná:',
    'found_outdated_packages' => '%d elavult csomag található',
];

