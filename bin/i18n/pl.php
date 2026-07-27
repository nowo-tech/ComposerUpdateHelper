<?php

declare(strict_types=1);
/**
 * Polish translations.
 */

return [
    // Main output messages
    'no_packages_update'       => 'Brak pakietów do aktualizacji',
    'all_up_to_date'           => 'wszystkie pakiety są aktualne',
    'all_have_conflicts'       => 'wszystkie przestarzałe pakiety mają konflikty zależności',
    'all_ignored'              => 'wszystkie przestarzałe pakiety są ignorowane',
    'all_ignored_or_conflicts' => 'wszystkie przestarzałe pakiety są ignorowane lub mają konflikty zależności',

    // Commands
    'suggested_commands'                => 'Sugerowane polecenia:',
    'suggested_commands_conflicts'      => 'Sugerowane polecenia do rozwiązania konfliktów zależności:',
    'suggested_commands_grouped'        => 'Sugerowane polecenia (spróbuj zainstalować razem - Composer może lepiej rozwiązać konflikty):',
    'grouped_install_explanation'       => '(Instalowanie wielu pakietów razem czasami pomaga Composer rozwiązać konflikty)',
    'grouped_install_warning'           => '(Uwaga: To nadal może się nie powieść, jeśli istnieją konflikty z zainstalowanymi pakietami, których nie można zaktualizować)',
    'copy_command_hint'                 => '(Select the command to copy)',
    'packages_need_maintainer_update'   => '(The following packages need updates from their maintainers)',
    'package_needs_update_for_grouped'  => '%s (installed: %s) needs update to support: %s (requires: %s)',
    'suggest_contact_maintainer'        => '💡 Consider contacting the maintainer of %s',
    'repository_url'                    => '📦 Repository: %s',
    'maintainers'                       => '👤 Maintainers: %s',
    'grouped_install_maintainer_needed' => 'Niektóre zainstalowane pakiety wymagają aktualizacji od ich maintainerów:',
    'package_needs_update'              => '%s: Wymaga aktualizacji do obsługi %s (wymaga: %s)',
    'grouped_install_warning'           => '(Note: This may still fail if there are conflicts with installed packages that cannot be updated)',
    'copy_command_hint'                 => '(Select the command to copy)',
    'packages_need_maintainer_update'   => '(The following packages need updates from their maintainers)',
    'package_needs_update_for_grouped'  => '%s (installed: %s) needs update to support: %s (requires: %s)',
    'suggest_contact_maintainer'        => '💡 Consider contacting the maintainer of %s',
    'repository_url'                    => '📦 Repository: %s',
    'maintainers'                       => '👤 Maintainers: %s',
    'includes_transitive'               => '(Zawiera zależności przechodnie potrzebne do rozwiązania konfliktów)',
    'update_transitive_first'           => '(Najpierw zaktualizuj te zależności przechodnie, a następnie spróbuj ponownie zaktualizować przefiltrowane pakiety)',

    // Framework and packages
    'detected_framework'                => 'Wykryte ograniczenia frameworka:',
    'ignored_packages_prod'             => 'Ignorowane pakiety (prod):',
    'ignored_packages_dev'              => 'Ignorowane pakiety (dev):',
    'dependency_analysis'               => 'Analiza weryfikacji zależności:',
    'all_outdated_before'               => 'Wszystkie przestarzałe pakiety (przed weryfikacją zależności):',
    'filtered_by_conflicts'             => 'Filtrowane według konfliktów zależności:',
    'suggested_transitive'              => 'Sugerowane aktualizacje zależności przechodnich do rozwiązania konfliktów:',
    'no_compatible_dependent_versions'  => 'Nie znaleziono zgodnych wersji pakietów zależnych:',
    'no_compatible_version_explanation' => '     - {depPackage}: Nie znaleziono wersji obsługującej {requiredBy}',
    'latest_checked_constraint'         => '       (Najnowsza sprawdzona wersja wymaga: {constraint})',
    'all_versions_require'              => '       (Wszystkie dostępne wersje wymagają: {constraint})',
    'packages_passed_check'             => 'Pakiety, które przeszły weryfikację zależności:',
    'none'                              => '(brak)',
    'conflicts_with'                    => 'konflikt z:',
    'package_abandoned'                 => 'Pakiet jest porzucony',
    'abandoned_packages_section'        => 'Znaleziono porzucone pakiety:',
    'all_installed_abandoned_section'   => 'Wszystkie zainstalowane porzucone pakiety:',
    'replaced_by'                       => 'zastąpiony przez: %s',
    'alternative_solutions'             => 'Rozwiązania alternatywne:',
    'compatible_with_conflicts'         => 'zgodny z konfliktowymi zależnościami',
    'alternative_packages'              => 'Alternatywne pakiety:',
    'recommended_replacement'           => 'zalecana zamiana',
    'similar_functionality'             => 'podobna funkcjonalność',

    // Debug messages
    'debug_show_release_info'     => 'showReleaseInfo = %s',
    'debug_check_dependencies'    => 'checkDependencies = %s',
    'debug_ignored_count'         => 'ignoredPackages count = %d',
    'debug_included_count'        => 'includedPackages count = %d',
    'debug_ignored_list'          => 'ignoredPackages list: %s',
    'debug_total_outdated'        => 'Total outdated packages: %d',
    'debug_require_packages'      => 'require packages: %d',
    'debug_require_dev_packages'  => 'require-dev packages: %d',
    'debug_detected_symfony'      => 'Detected Symfony constraint: %s (from extra.symfony.require)',
    'debug_processing_package'    => 'Processing package: %s (installed: %s, latest: %s)',
    'debug_action_ignored'        => 'Action: IGNORED (in ignore list and not in include list)',
    'debug_action_skipped'        => 'Action: SKIPPED (no compatible version found due to dependency constraints)',
    'debug_action_added'          => 'Action: ADDED to %s dependencies: %s',
    'debug_no_compatible_version' => 'No compatible version found for %s (proposed: %s)',

    // Release info
    'release_info'           => 'Informacje o Wersji',
    'release_changelog'      => 'Dziennik Zmian',
    'release_view_on_github' => 'Zobacz na GitHub',

    // Progress messages
    'checking_dependency_conflicts'   => '⏳ Sprawdzanie konfliktów zależności...',
    'checking_abandoned_packages'     => '⏳ Sprawdzanie porzuconych pakietów...',
    'checking_all_abandoned_packages' => '⏳ Sprawdzanie wszystkich zainstalowanych pakietów pod kątem statusu porzuconych...',
    'searching_fallback_versions'     => '⏳ Wyszukiwanie wersji zapasowych...',
    'searching_alternative_packages'  => '⏳ Wyszukiwanie alternatywnych pakietów...',
    'checking_maintainer_info'        => '⏳ Sprawdzanie informacji o maintainerze...',

    // Impact analysis
    'impact_analysis'         => 'Analiza wpływu: Aktualizacja {package} do {version} wpłynęłaby na:',
    'impact_analysis_saved'   => '✅ Analiza wpływu zapisana w: %s',
    'found_outdated_packages' => 'Znaleziono %d przestarzały(ch) pakiet(ów)',
];
