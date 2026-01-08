<?php
/**
 * English translations (default)
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'No packages to update',
    'all_up_to_date' => 'all packages are up to date',
    'all_have_conflicts' => 'all outdated packages have dependency conflicts',
    'all_ignored' => 'all outdated packages are ignored',
    'all_ignored_or_conflicts' => 'all outdated packages are ignored or have dependency conflicts',
    
    // Commands
    'suggested_commands' => 'Suggested commands:',
    'suggested_commands_conflicts' => 'Suggested commands to resolve dependency conflicts:',
    'includes_transitive' => '(Includes transitive dependencies needed to resolve conflicts)',
    'update_transitive_first' => '(Update these transitive dependencies first, then retry updating the filtered packages)',
    
    // Framework and packages
    'detected_framework' => 'Detected framework constraints:',
    'ignored_packages_prod' => 'Ignored packages (prod):',
    'ignored_packages_dev' => 'Ignored packages (dev):',
    'dependency_analysis' => 'Dependency checking analysis:',
    'all_outdated_before' => 'All outdated packages (before dependency check):',
    'filtered_by_conflicts' => 'Filtered by dependency conflicts:',
    'suggested_transitive' => 'Suggested transitive dependency updates to resolve conflicts:',
    'packages_passed_check' => 'Packages that passed dependency check:',
    'none' => '(none)',
    'conflicts_with' => 'conflicts with:',
    
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
    'release_info' => 'Release Information',
    'release_changelog' => 'Changelog',
    'release_view_on_github' => 'View on GitHub',
];

