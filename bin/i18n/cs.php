<?php
/**
 * Czech translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Žádné balíčky k aktualizaci',
    'all_up_to_date' => 'všechny balíčky jsou aktuální',
    'all_have_conflicts' => 'všechny zastaralé balíčky mají konflikty závislostí',
    'all_ignored' => 'všechny zastaralé balíčky jsou ignorovány',
    'all_ignored_or_conflicts' => 'všechny zastaralé balíčky jsou ignorovány nebo mají konflikty závislostí',
    
    // Commands
    'suggested_commands' => 'Doporučené příkazy:',
    'suggested_commands_conflicts' => 'Doporučené příkazy k vyřešení konfliktů závislostí:',
    'suggested_commands_grouped' => 'Doporučené příkazy (zkuste nainstalovat společně - Composer může lépe vyřešit konflikty):',
    'grouped_install_explanation' => '(Instalace více balíčků společně někdy pomáhá Composeru vyřešit konflikty)',
    'grouped_install_warning' => '(Poznámka: To může stále selhat, pokud existují konflikty s nainstalovanými balíčky, které nelze aktualizovat)',
    'copy_command_hint' => '(Click to copy or select the command)',
    'packages_need_maintainer_update' => '(The following packages need updates from their maintainers)',
    'package_needs_update_for_grouped' => '%s (installed: %s) needs update to support: %s (requires: %s)',
    'suggest_contact_maintainer' => '💡 Consider contacting the maintainer of %s',
    'repository_url' => '📦 Repository: %s',
    'maintainers' => '👤 Maintainers: %s',
    'includes_transitive' => '(Zahrnuje tranzitivní závislosti potřebné k vyřešení konfliktů)',
    'update_transitive_first' => '(Nejprve aktualizujte tyto tranzitivní závislosti, poté zkuste znovu aktualizovat filtrované balíčky)',
    
    // Framework and packages
    'detected_framework' => 'Detekovaná omezení frameworku:',
    'ignored_packages_prod' => 'Ignorované balíčky (prod):',
    'ignored_packages_dev' => 'Ignorované balíčky (dev):',
    'dependency_analysis' => 'Analýza kontroly závislostí:',
    'all_outdated_before' => 'Všechny zastaralé balíčky (před kontrolou závislostí):',
    'filtered_by_conflicts' => 'Filtrováno podle konfliktů závislostí:',
    'suggested_transitive' => 'Doporučené aktualizace tranzitivních závislostí k vyřešení konfliktů:',
    'no_compatible_dependent_versions' => 'Nebyly nalezeny kompatibilní verze závislých balíčků:',
    'no_compatible_version_explanation' => '     - {depPackage}: Nebyla nalezena verze, která podporuje {requiredBy}',
    'latest_checked_constraint' => '       (Nejnovější zkontrolovaná verze vyžaduje: {constraint})',
    'all_versions_require' => '       (Všechny dostupné verze vyžadují: {constraint})',
    'packages_passed_check' => 'Balíčky, které prošly kontrolou závislostí:',
    'none' => '(žádné)',
    'conflicts_with' => 'konfliktuje s:',
    'package_abandoned' => 'Balíček je opuštěn',
    'abandoned_packages_section' => 'Nalezeny opuštěné balíčky:',
    'all_installed_abandoned_section' => 'Všechny nainstalované opuštěné balíčky:',
    'replaced_by' => 'nahrazen: %s',
    'alternative_solutions' => 'Alternativní řešení:',
    'compatible_with_conflicts' => 'kompatibilní s konfliktními závislostmi',
    'alternative_packages' => 'Alternativní balíčky:',
    'recommended_replacement' => 'doporučená náhrada',
    'similar_functionality' => 'podobná funkcionalita',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Celkem zastaralých balíčků: %d',
    'debug_require_packages' => 'require balíčky: %d',
    'debug_require_dev_packages' => 'require-dev balíčky: %d',
    'debug_detected_symfony' => 'Detekované Symfony omezení: %s (z extra.symfony.require)',
    'debug_processing_package' => 'Zpracování balíčku: %s (nainstalováno: %s, nejnovější: %s)',
    'debug_action_ignored' => 'Akce: IGNOROVÁNO (v seznamu ignorovaných a ne v seznamu zahrnutých)',
    'debug_action_skipped' => 'Akce: PŘESKOČENO (nenalezena kompatibilní verze kvůli omezením závislostí)',
    'debug_action_added' => 'Akce: PŘIDÁNO do %s závislostí: %s',
    'debug_no_compatible_version' => 'Nenalezena kompatibilní verze pro %s (navrženo: %s)',
    
    // Release info
    'release_info' => 'Informace o verzi',
    'release_changelog' => 'Seznam změn',
    'release_view_on_github' => 'Zobrazit na GitHubu',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Kontrola konfliktů závislostí...',
    'checking_abandoned_packages' => '⏳ Kontrola opuštěných balíčků...',
    'checking_all_abandoned_packages' => '⏳ Kontrola všech nainstalovaných balíčků na opuštěný stav...',
    'searching_fallback_versions' => '⏳ Vyhledávání záložních verzí...',
    'searching_alternative_packages' => '⏳ Vyhledávání alternativních balíčků...',
    'checking_maintainer_info' => '⏳ Kontrola informací o správci...',
    
    // Impact analysis
    'impact_analysis' => 'Analýza dopadu: Aktualizace {package} na {version} by ovlivnila:',
    'impact_analysis_saved' => '✅ Analýza dopadu uložena do: %s',
    'found_outdated_packages' => 'Nalezeno %d zastaralých balíčků',
];

