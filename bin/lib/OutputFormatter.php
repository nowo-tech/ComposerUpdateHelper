<?php

declare(strict_types=1);

/**
 * Output Formatter
 * Formats and generates output for process-updates.php
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

class OutputFormatter
{
    /**
     * Format and generate output
     *
     * @param array $data All data needed for output formatting
     * @param array $options Output options (debug, verbose, checkDependencies, showReleaseInfo, showReleaseDetail, detectedLang)
     * @return array Array of output lines
     */
    public static function formatOutput(array $data, array $options = []): array
    {
        $prod = $data['prod'] ?? [];
        $dev = $data['dev'] ?? [];
        $ignoredProd = $data['ignoredProd'] ?? [];
        $ignoredDev = $data['ignoredDev'] ?? [];
        $frameworkConstraints = $data['frameworkConstraints'] ?? [];
        $allOutdatedProd = $data['allOutdatedProd'] ?? [];
        $allOutdatedDev = $data['allOutdatedDev'] ?? [];
        $filteredByDependenciesProd = $data['filteredByDependenciesProd'] ?? [];
        $filteredByDependenciesDev = $data['filteredByDependenciesDev'] ?? [];
        $filteredPackageConflicts = $data['filteredPackageConflicts'] ?? [];
        $filteredPackageAbandoned = $data['filteredPackageAbandoned'] ?? [];
        $filteredPackageFallbacks = $data['filteredPackageFallbacks'] ?? [];
        $filteredPackageAlternatives = $data['filteredPackageAlternatives'] ?? [];
        $filteredPackageMaintainerContacts = $data['filteredPackageMaintainerContacts'] ?? [];
        $filteredPackageImpact = $data['filteredPackageImpact'] ?? [];
        $requiredTransitiveUpdates = $data['requiredTransitiveUpdates'] ?? [];
        $packagesWithCompatibleDependents = $data['packagesWithCompatibleDependents'] ?? [];
        $checkedDependentsWithoutCompatible = $data['checkedDependentsWithoutCompatible'] ?? [];
        $releaseInfo = $data['releaseInfo'] ?? [];
        $devSet = $data['devSet'] ?? [];
        $allInstalledAbandoned = $data['allInstalledAbandoned'] ?? [];

        $debug = $options['debug'] ?? false;
        $verbose = $options['verbose'] ?? false;
        $checkDependencies = $options['checkDependencies'] ?? true;
        $showReleaseInfo = $options['showReleaseInfo'] ?? false;
        $showReleaseDetail = $options['showReleaseDetail'] ?? false;
        $showImpactAnalysis = $options['showImpactAnalysis'] ?? false;
        $detectedLang = $options['detectedLang'] ?? null;

        // Check if there's anything to show
        if (empty($prod) && empty($dev) && empty($ignoredProd) && empty($ignoredDev)) {
            if ($verbose || $debug) {
                error_log("ℹ️  No outdated direct dependencies found.");
            }
            return [" " . E_OK . "  No outdated direct dependencies."];
        }

        $output = [];

        // Show detected frameworks
        $detectedFrameworks = [];
        foreach ($frameworkConstraints as $prefix => $version) {
            $detectedFrameworks[] = rtrim($prefix, '/') . ' ' . $version . '.*';
        }
        if (!empty($detectedFrameworks)) {
            $msg = function_exists('t') ? t('detected_framework', [], $detectedLang) : 'Detected framework constraints:';
            $output[] = " " . E_WRENCH . "  " . $msg;
            if ($debug) {
                error_log("DEBUG: i18n - Using translation for 'detected_framework': " . $msg);
            }
            foreach ($detectedFrameworks as $fw) {
                $output[] = "  - " . $fw;
            }
            $output[] = "";
        }

        // Show ignored packages (prod)
        if (!empty($ignoredProd)) {
            $msg = function_exists('t') ? t('ignored_packages_prod', [], $detectedLang) : 'Ignored packages (prod):';
            $output[] = " " . E_SKIP . "   " . $msg;
            if ($debug) {
                error_log("DEBUG: i18n - Using translation for 'ignored_packages_prod': " . $msg);
            }
            foreach ($ignoredProd as $pkg) {
                $output[] = "  - " . $pkg;
            }
            $output[] = "";
        }

        // Show ignored packages (dev)
        if (!empty($ignoredDev)) {
            $msg = function_exists('t') ? t('ignored_packages_dev', [], $detectedLang) : 'Ignored packages (dev):';
            $output[] = " " . E_SKIP . "   " . $msg;
            if ($debug) {
                error_log("DEBUG: i18n - Using translation for 'ignored_packages_dev': " . $msg);
            }
            foreach ($ignoredDev as $pkg) {
                $output[] = "  - " . $pkg;
            }
            $output[] = "";
        }

        // Show all installed abandoned packages section (if any) - BEFORE dependency analysis
        if (!empty($allInstalledAbandoned)) {
            $msg = function_exists('t') ? t('all_installed_abandoned_section', [], $detectedLang) : 'All abandoned packages installed:';
            $output[] = " " . E_WARNING . " " . $msg;
            if ($debug) {
                error_log("DEBUG: i18n - Using translation for 'all_installed_abandoned_section': " . $msg);
            }

            // Separate abandoned packages by prod/dev
            $abandonedProd = [];
            $abandonedDev = [];

            foreach ($allInstalledAbandoned as $packageString => $abandonedInfo) {
                $packageName = explode(':', $packageString)[0];
                $isDev = $abandonedInfo['is_dev'] ?? false;

                $abandonedMsg = function_exists('t') ? t('package_abandoned', [], $detectedLang) : 'Package is abandoned';
                $info = " (⚠️ " . $abandonedMsg;
                if ($abandonedInfo['replacement']) {
                    $replacedByMsg = function_exists('t') ? t('replaced_by', [$abandonedInfo['replacement']], $detectedLang) : "replaced by: {$abandonedInfo['replacement']}";
                    $info .= ", " . $replacedByMsg;
                }
                $info .= ")";

                if ($isDev) {
                    $abandonedDev[] = $packageString . " " . LABEL_DEV . $info;
                } else {
                    $abandonedProd[] = $packageString . " " . LABEL_PROD . $info;
                }
            }

            if (!empty($abandonedProd)) {
                foreach ($abandonedProd as $pkg) {
                    $output[] = "     - " . $pkg;
                }
            }
            if (!empty($abandonedDev)) {
                foreach ($abandonedDev as $pkg) {
                    $output[] = "     - " . $pkg;
                }
            }
            $output[] = "";
        }

        // Show dependency checking comparison when enabled
        if ($checkDependencies) {
            $msg = function_exists('t') ? t('dependency_analysis', [], $detectedLang) : 'Dependency checking analysis:';
            $output[] = " " . E_WRENCH . " " . $msg;
            if ($debug) {
                error_log("DEBUG: i18n - Using translation for 'dependency_analysis': " . $msg);
            }

            // Show all outdated packages (before checking)
            if (!empty($allOutdatedProd) || !empty($allOutdatedDev)) {
                $msg = function_exists('t') ? t('all_outdated_before', [], $detectedLang) : 'All outdated packages (before dependency check):';
                $output[] = "  " . E_CLIPBOARD . " " . $msg;
                $output = array_merge($output, Utils::formatPackageList($allOutdatedProd, LABEL_PROD));
                $output = array_merge($output, Utils::formatPackageList($allOutdatedDev, LABEL_DEV));
                $output[] = "";
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'all_outdated_before': " . $msg);
                }
            } else {
                $msg = function_exists('t') ? t('all_outdated_before', [], $detectedLang) : 'All outdated packages (before dependency check):';
                $noneLabel = function_exists('t') ? t('none', [], $detectedLang) : LABEL_NONE;
                $output[] = "  " . E_CLIPBOARD . " " . $msg . " " . $noneLabel;
                $output[] = "";
            }

            // Show abandoned packages section (if any)
            if (!empty($filteredPackageAbandoned)) {
                $msg = function_exists('t') ? t('abandoned_packages_section', [], $detectedLang) : 'Abandoned packages found:';
                $output[] = "  " . E_WARNING . " " . $msg;
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'abandoned_packages_section': " . $msg);
                }

                // Separate abandoned packages by prod/dev
                $abandonedProd = [];
                $abandonedDev = [];
                $devSet = $data['devSet'] ?? [];

                foreach ($filteredPackageAbandoned as $packageString => $abandonedInfo) {
                    $packageName = explode(':', $packageString)[0];
                    $isDev = isset($devSet[$packageName]);

                    $abandonedMsg = function_exists('t') ? t('package_abandoned', [], $detectedLang) : 'Package is abandoned';
                    $info = " (⚠️ " . $abandonedMsg;
                    if ($abandonedInfo['replacement']) {
                        $replacedByMsg = function_exists('t') ? t('replaced_by', [$abandonedInfo['replacement']], $detectedLang) : "replaced by: {$abandonedInfo['replacement']}";
                        $info .= ", " . $replacedByMsg;
                    }
                    $info .= ")";

                    if ($isDev) {
                        $abandonedDev[] = $packageString . " " . LABEL_DEV . $info;
                    } else {
                        $abandonedProd[] = $packageString . " " . LABEL_PROD . $info;
                    }
                }

                if (!empty($abandonedProd)) {
                    foreach ($abandonedProd as $pkg) {
                        $output[] = "     - " . $pkg;
                    }
                }
                if (!empty($abandonedDev)) {
                    foreach ($abandonedDev as $pkg) {
                        $output[] = "     - " . $pkg;
                    }
                }
                $output[] = "";
            }

            // Show filtered packages (conflicts detected)
            if (!empty($filteredByDependenciesProd) || !empty($filteredByDependenciesDev)) {
                if ($debug) {
                    error_log("DEBUG: Generating output for filtered packages:");
                    error_log("DEBUG:   - Filtered prod packages: " . count($filteredByDependenciesProd));
                    error_log("DEBUG:   - Filtered dev packages: " . count($filteredByDependenciesDev));
                    error_log("DEBUG:   - Packages with conflict info: " . count($filteredPackageConflicts));
                }
                $msg = function_exists('t') ? t('filtered_by_conflicts', [], $detectedLang) : 'Filtered by dependency conflicts:';
                $output[] = "  " . E_WARNING . " " . $msg;
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'filtered_by_conflicts': " . $msg);
                }
                // Show prod packages with conflicts
                foreach ($filteredByDependenciesProd as $pkg) {
                    $conflictInfo = self::buildConflictInfo($pkg, $filteredPackageConflicts, $filteredPackageAbandoned, $detectedLang, $debug);
                    $output[] = "     - " . $pkg . " " . LABEL_PROD . $conflictInfo;
                }
                // Show dev packages with conflicts
                foreach ($filteredByDependenciesDev as $pkg) {
                    $conflictInfo = self::buildConflictInfo($pkg, $filteredPackageConflicts, $filteredPackageAbandoned, $detectedLang, $debug);
                    $output[] = "     - " . $pkg . " " . LABEL_DEV . $conflictInfo;
                }
                $output[] = "";

                // Show explanation when no compatible dependent versions were found
                if (!empty($checkedDependentsWithoutCompatible)) {
                    $infoMsg = function_exists('t') ? t('no_compatible_dependent_versions', [], $detectedLang) : 'No compatible versions of dependent packages found:';
                    $output[] = "  " . E_INFO . " " . $infoMsg;
                    if ($debug) {
                        error_log("DEBUG: i18n - Using translation for 'no_compatible_dependent_versions': " . $infoMsg);
                    }

                    foreach ($checkedDependentsWithoutCompatible as $depPackage => $info) {
                        $requiredBy = $info['required_by'];
                        $currentConstraint = $info['current_constraint'];
                        $latestConstraint = $info['latest_checked_constraint'];

                        $explanationMsg = function_exists('t') ? t('no_compatible_version_explanation', ['depPackage' => $depPackage, 'requiredBy' => $requiredBy, 'constraint' => $latestConstraint], $detectedLang) :
                            "     - {$depPackage}: No version found that supports {$requiredBy}";
                        $output[] = $explanationMsg;

                        if ($latestConstraint !== $currentConstraint) {
                            $constraintMsg = function_exists('t') ? t('latest_checked_constraint', ['constraint' => $latestConstraint], $detectedLang) :
                                "       (Latest checked version requires: {$latestConstraint})";
                            $output[] = $constraintMsg;
                        } else {
                            $allVersionsMsg = function_exists('t') ? t('all_versions_require', ['constraint' => $currentConstraint], $detectedLang) :
                                "       (All available versions require: {$currentConstraint})";
                            $output[] = $allVersionsMsg;
                        }
                    }
                    $output[] = "";
                }

                // Show packages that need maintainer updates for grouped installation
                $groupedMaintainerContacts = $data['groupedMaintainerContacts'] ?? [];
                if (!empty($groupedMaintainerContacts)) {
                    $maintainerMsg = function_exists('t') ? t('packages_need_maintainer_update', [], $detectedLang) : 'The following packages need updates from their maintainers to support the grouped installation:';
                    $output[] = "  " . E_INFO . " " . $maintainerMsg;
                    if ($debug) {
                        error_log("DEBUG: i18n - Using translation for 'packages_need_maintainer_update': " . $maintainerMsg);
                    }

                    foreach ($groupedMaintainerContacts as $pkgName => $maintainerInfo) {
                        $conflictInfo = $maintainerInfo['conflict_info'] ?? [];
                        $requiredBy = !empty($conflictInfo['required_by']) ? implode(', ', array_unique($conflictInfo['required_by'])) : '';
                        $constraints = !empty($conflictInfo['required_constraints']) ? implode(' OR ', $conflictInfo['required_constraints']) : '';
                        $installedVersion = $conflictInfo['installed_version'] ?? 'unknown';

                        $pkgMsg = function_exists('t') ? t('package_needs_update_for_grouped', ['pkgName' => $pkgName, 'installedVersion' => $installedVersion, 'requiredBy' => $requiredBy, 'constraints' => $constraints], $detectedLang) :
                            "     - {$pkgName} (installed: {$installedVersion}) needs update to support: {$requiredBy} (requires: {$constraints})";
                        $output[] = $pkgMsg;

                        $contactMsg = function_exists('t') ? t('suggest_contact_maintainer', ['pkgName' => $pkgName], $detectedLang) :
                            "       💡 Consider contacting the maintainer of {$pkgName} to request support for these versions";
                        $output[] = $contactMsg;

                        if (!empty($maintainerInfo['repository_url'])) {
                            $repoMsg = function_exists('t') ? t('repository_url', ['url' => $maintainerInfo['repository_url']], $detectedLang) :
                                "       📦 Repository: {$maintainerInfo['repository_url']}";
                            $output[] = $repoMsg;
                        }

                        if (!empty($maintainerInfo['maintainers'])) {
                            $maintainers = [];
                            foreach ($maintainerInfo['maintainers'] as $maint) {
                                if (!empty($maint['email'])) {
                                    $maintainers[] = $maint['name'] . ' <' . $maint['email'] . '>';
                                } else {
                                    $maintainers[] = $maint['name'];
                                }
                            }
                            if (!empty($maintainers)) {
                                $maintMsg = function_exists('t') ? t('maintainers', ['maintainers' => implode(', ', $maintainers)], $detectedLang) :
                                    "       👤 Maintainers: " . implode(', ', $maintainers);
                                $output[] = $maintMsg;
                            }
                        }
                    }
                    $output[] = "";
                }

                // Show impact analysis if available and enabled
                if ($showImpactAnalysis && !empty($filteredPackageImpact)) {
                    foreach ($filteredPackageImpact as $packageString => $impact) {
                        if ($impact['total_affected'] > 0) {
                            $packageName = explode(':', $packageString)[0];
                            $newVersion = $impact['new_version'];
                            $impactMsg = function_exists('t') ? t('impact_analysis', [], $detectedLang) : 'Impact analysis: Updating {package} to {version} would affect:';
                            $impactMsg = str_replace(['{package}', '{version}'], [$packageName, $newVersion], $impactMsg);
                            $output[] = "  " . E_CLIPBOARD . " " . $impactMsg;

                            // Show direct affected packages
                            foreach ($impact['direct_affected'] as $affected) {
                                $output[] = "     - {$affected['package']} ({$affected['reason']})";
                            }

                            // Show transitive affected packages
                            foreach ($impact['transitive_affected'] as $affected) {
                                $output[] = "     - {$affected['package']} ({$affected['reason']})";
                            }
                            $output[] = "";
                        }
                    }
                }

                // Show fallback version suggestions if available
                if (!empty($filteredPackageFallbacks)) {
                    $msg = function_exists('t') ? t('alternative_solutions', [], $detectedLang) : 'Alternative solutions:';
                    $output[] = "  " . E_BULB . " " . $msg;
                    if ($debug) {
                        error_log("DEBUG: i18n - Using translation for 'alternative_solutions': " . $msg);
                    }
                    foreach ($filteredPackageFallbacks as $packageString => $fallbackVersion) {
                        // Extract package name from packageString (format: "name:version")
                        $packageName = explode(':', $packageString)[0];
                        $compatibleMsg = function_exists('t') ? t('compatible_with_conflicts', [], $detectedLang) : 'compatible with conflicting dependencies';
                        $output[] = "     - {$packageName}:{$fallbackVersion} ({$compatibleMsg})";
                    }
                    $output[] = "";
                }

                // Show alternative package suggestions if available
                if (!empty($filteredPackageAlternatives)) {
                    $msg = function_exists('t') ? t('alternative_packages', [], $detectedLang) : 'Alternative packages:';
                    $output[] = "  " . E_BULB . " " . $msg;
                    if ($debug) {
                        error_log("DEBUG: i18n - Using translation for 'alternative_packages': " . $msg);
                    }
                    foreach ($filteredPackageAlternatives as $packageString => $altInfo) {
                        // Extract package name from packageString (format: "name:version")
                        $packageName = explode(':', $packageString)[0];
                        $reasonMsg = '';
                        if (isset($altInfo['reason']) && $altInfo['reason'] === 'abandoned_replacement') {
                            $reasonMsg = ' (' . (function_exists('t') ? t('recommended_replacement', [], $detectedLang) : 'recommended replacement') . ')';
                        } elseif (isset($altInfo['reason']) && $altInfo['reason'] === 'similar_packages') {
                            $reasonMsg = ' (' . (function_exists('t') ? t('similar_functionality', [], $detectedLang) : 'similar functionality') . ')';
                        }

                        foreach ($altInfo['alternatives'] as $alt) {
                            $altName = $alt['name'];
                            $altDesc = !empty($alt['description']) ? " - {$alt['description']}" : '';
                            $output[] = "     - {$altName}{$reasonMsg}{$altDesc}";
                        }
                    }
                    $output[] = "";
                }

                // Show maintainer contact suggestions if available
                if (!empty($filteredPackageMaintainerContacts)) {
                    $msg = function_exists('t') ? t('maintainer_contact_suggested', [], $detectedLang) : 'No automatic solution available - Manual intervention required:';
                    $output[] = "  " . E_WARNING . " " . $msg;
                    if ($debug) {
                        error_log("DEBUG: i18n - Using translation for 'maintainer_contact_suggested': " . $msg);
                    }
                    foreach ($filteredPackageMaintainerContacts as $packageString => $contactInfo) {
                        // Extract package name from packageString (format: "name:version")
                        $packageName = explode(':', $packageString)[0];

                        $packageMsg = function_exists('t') ? t('package', [], $detectedLang) : 'Package:';
                        $output[] = "     " . E_PACKAGE . " {$packageMsg} {$packageName}";

                        // Show maintainers
                        if (!empty($contactInfo['maintainers'])) {
                            $maintainersMsg = function_exists('t') ? t('contact_maintainers', [], $detectedLang) : 'Contact package maintainer(s):';
                            $output[] = "     " . E_BULB . " {$maintainersMsg}";
                            foreach ($contactInfo['maintainers'] as $maintainer) {
                                $maintainerLine = "        - " . ($maintainer['name'] ?? 'Unknown');
                                if (!empty($maintainer['email'])) {
                                    $maintainerLine .= " ({$maintainer['email']})";
                                }
                                if (!empty($maintainer['homepage'])) {
                                    $maintainerLine .= " - {$maintainer['homepage']}";
                                }
                                $output[] = $maintainerLine;
                            }
                        }

                        // Show repository issue URL
                        if (!empty($contactInfo['repository_url']) && !empty($contactInfo['repository_type'])) {
                            $issueUrl = MaintainerContactFinder::generateIssueUrl(
                                $contactInfo['repository_url'],
                                $contactInfo['repository_type']
                            );
                            if ($issueUrl) {
                                $openIssueMsg = function_exists('t') ? t('open_issue_repository', [], $detectedLang) : 'Open issue on repository:';
                                $output[] = "     " . E_LINK . " {$openIssueMsg}";
                                $output[] = "        - {$issueUrl}";
                            }
                        }

                        // Show stale package warning
                        if (!empty($contactInfo['is_stale']) && !empty($contactInfo['last_update'])) {
                            $lastUpdateDate = date('Y-m-d', strtotime($contactInfo['last_update']));
                            $staleWarningMsg = function_exists('t') ? t('package_stale_warning', ['date' => $lastUpdateDate], $detectedLang) : "⚠️  Note: This package hasn't been updated since {$lastUpdateDate} (over 2 years ago).";
                            $output[] = "     " . $staleWarningMsg;
                            $staleSuggestionsMsg = function_exists('t') ? t('stale_package_suggestions', [], $detectedLang) : "Consider: Finding an alternative package, Forking and maintaining yourself, or Contacting maintainer about maintenance status";
                            $output[] = "        " . $staleSuggestionsMsg;
                        }

                        $output[] = "";
                    }
                    $output[] = "";
                }

                // Show transitive dependencies that need updates
                if (!empty($requiredTransitiveUpdates)) {
                    $msg = function_exists('t') ? t('suggested_transitive', [], $detectedLang) : 'Suggested transitive dependency updates to resolve conflicts:';
                    $output[] = "  " . E_BULB . " " . $msg;
                    if ($debug) {
                        error_log("DEBUG: i18n - Using translation for 'suggested_transitive': " . $msg);
                    }
                    foreach ($requiredTransitiveUpdates as $transitivePkg => $info) {
                        $requiredByList = implode(', ', array_unique($info['required_by']));
                        $output[] = "     - {$transitivePkg}:{$info['suggested_version']} (installed: {$info['installed_version']}, required by: {$requiredByList})";
                    }
                    $output[] = "";
                }
            } else {
                $msg = function_exists('t') ? t('filtered_by_conflicts', [], $detectedLang) : 'Filtered by dependency conflicts:';
                $noneLabel = function_exists('t') ? t('none', [], $detectedLang) : LABEL_NONE;
                $output[] = "  " . E_WARNING . " " . $msg . " " . $noneLabel;
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'filtered_by_conflicts' (none): " . $msg);
                }
                $output[] = "";
            }

            // Show packages that passed dependency check
            if (!empty($prod) || !empty($dev)) {
                $msg = function_exists('t') ? t('packages_passed_check', [], $detectedLang) : 'Packages that passed dependency check:';
                $output[] = "  " . E_OK . " " . $msg;
                $output = array_merge($output, Utils::formatPackageList($prod, LABEL_PROD));
                $output = array_merge($output, Utils::formatPackageList($dev, LABEL_DEV));
                $output[] = "";
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'packages_passed_check': " . $msg);
                }
            } else {
                $msg = function_exists('t') ? t('packages_passed_check', [], $detectedLang) : 'Packages that passed dependency check:';
                $noneLabel = function_exists('t') ? t('none', [], $detectedLang) : LABEL_NONE;
                $output[] = "  " . E_OK . " " . $msg . " " . $noneLabel;
                $output[] = "";
            }
        }

        // Build commands list
        $hasGroupedFilteredCommands = [];
        $allInstalledPackages = $data['allInstalledPackages'] ?? [];
        $groupedMaintainerContacts = [];
        $commandsList = self::buildCommandsList($prod, $dev, $filteredByDependenciesProd, $filteredByDependenciesDev, $requiredTransitiveUpdates, $packagesWithCompatibleDependents, $devSet, $debug, $hasGroupedFilteredCommands, $allInstalledPackages, $groupedMaintainerContacts);

        // Add grouped maintainer contacts to data for output
        $data['groupedMaintainerContacts'] = $groupedMaintainerContacts;

        // Add grouped maintainer contacts to data for output formatting
        $data['groupedMaintainerContacts'] = $groupedMaintainerContacts;

        // Add commands output
        $output = array_merge($output, self::formatCommandsOutput($commandsList, $prod, $dev, $requiredTransitiveUpdates, $allOutdatedProd, $allOutdatedDev, $filteredByDependenciesProd, $filteredByDependenciesDev, $ignoredProd, $ignoredDev, $detectedLang, $debug, $data));

        // Add release information
        if ($showReleaseInfo && !empty($releaseInfo)) {
            $output = array_merge($output, self::formatReleaseInfo($releaseInfo, $showReleaseDetail));
        }

        return $output;
    }

    /**
     * Build conflict information string
     *
     * @param string $pkg Package string (format: "package:version")
     * @param array $filteredPackageConflicts Conflicts array
     * @param array $filteredPackageAbandoned Abandoned info array
     * @param string|null $detectedLang Detected language
     * @param bool $debug Debug mode
     * @return string Conflict information string
     */
    private static function buildConflictInfo(string $pkg, array $filteredPackageConflicts, array $filteredPackageAbandoned, ?string $detectedLang, bool $debug): string
    {
        $conflictInfo = "";
        if (isset($filteredPackageConflicts[$pkg]) && !empty($filteredPackageConflicts[$pkg])) {
            $conflictCount = count($filteredPackageConflicts[$pkg]);
            $conflictList = [];
            // Extract package name from pkg string (format: "package:version")
            $pkgName = explode(':', $pkg)[0];
            foreach ($filteredPackageConflicts[$pkg] as $depPkg => $constraint) {
                $conflictList[] = "{$depPkg} requires {$pkgName} {$constraint}";
            }
            $conflictInfo = " (conflicts with " . $conflictCount . " package" . ($conflictCount > 1 ? "s" : "") . ": " . implode(', ', $conflictList) . ")";
            if ($debug) {
                error_log("DEBUG:   - {$pkg} conflicts with: " . implode(', ', array_keys($filteredPackageConflicts[$pkg])));
            }
        } elseif ($debug) {
            error_log("DEBUG:   - {$pkg} has no conflict info stored (may have requirement conflicts instead of dependent conflicts)");
        }

        // Add abandoned warning if applicable
        if (isset($filteredPackageAbandoned[$pkg])) {
            $abandonedInfo = $filteredPackageAbandoned[$pkg];
            $abandonedMsg = function_exists('t') ? t('package_abandoned', [], $detectedLang) : 'Package is abandoned';
            $conflictInfo .= " (⚠️ " . $abandonedMsg;
            if ($abandonedInfo['replacement']) {
                $replacedByMsg = function_exists('t') ? t('replaced_by', ['package' => $abandonedInfo['replacement']], $detectedLang) : "replaced by: {$abandonedInfo['replacement']}";
                $conflictInfo .= ", " . $replacedByMsg;
            }
            $conflictInfo .= ")";
        }

        return $conflictInfo;
    }

    /**
     * Build commands list
     *
     * @param array $prod Production packages
     * @param array $dev Development packages
     * @param array $filteredByDependenciesProd Filtered production packages
     * @param array $filteredByDependenciesDev Filtered development packages
     * @param array $requiredTransitiveUpdates Required transitive updates
     * @param array $devSet Dev set for checking
     * @param bool $debug Debug mode
     * @return array Commands list
     */
    private static function buildCommandsList(array $prod, array $dev, array $filteredByDependenciesProd, array $filteredByDependenciesDev, array $requiredTransitiveUpdates, array $packagesWithCompatibleDependents, array $devSet, bool $debug, ?array &$hasGroupedFilteredCommands = null, array $allInstalledPackages = [], ?array &$groupedMaintainerContacts = null): array
    {
        $commandsList = [];
        $prodCommand = Utils::buildComposerCommand($prod, false);
        if ($prodCommand !== null) {
            $commandsList[] = $prodCommand;
        }
        $devCommand = Utils::buildComposerCommand($dev, true);
        if ($devCommand !== null) {
            $commandsList[] = $devCommand;
        }

        // Add commands for transitive dependencies if needed
        // When there are filtered packages and transitive dependencies, include both in the same command
        if (!empty($requiredTransitiveUpdates)) {
            $transitiveProd = [];
            $transitiveDev = [];

            foreach ($requiredTransitiveUpdates as $transitivePkg => $info) {
                $pkgString = $transitivePkg . ':' . $info['suggested_version'];
                // Determine if it's a dev dependency by checking if it's in require-dev
                if (isset($devSet[$transitivePkg])) {
                    $transitiveDev[] = $pkgString;
                } else {
                    $transitiveProd[] = $pkgString;
                }
            }

            // If there are filtered packages, include them in the same command as transitive dependencies
            // This ensures all related packages are updated together
            // Also include packages that have compatible dependent versions found
            $filteredProdWithCompatible = $filteredByDependenciesProd;
            $filteredDevWithCompatible = $filteredByDependenciesDev;

            // Add packages with compatible dependents to the filtered lists (they should be included in commands)
            foreach ($packagesWithCompatibleDependents as $packageString => $hasCompatible) {
                if ($hasCompatible) {
                    $packageName = explode(':', $packageString)[0];
                    if (isset($devSet[$packageName])) {
                        if (!in_array($packageString, $filteredDevWithCompatible)) {
                            $filteredDevWithCompatible[] = $packageString;
                        }
                    } else {
                        if (!in_array($packageString, $filteredProdWithCompatible)) {
                            $filteredProdWithCompatible[] = $packageString;
                        }
                    }
                }
            }

            if (!empty($filteredProdWithCompatible) || !empty($filteredDevWithCompatible)) {
                // Merge filtered packages (including those with compatible dependents) with transitive dependencies and remove duplicates
                $allProd = array_unique(array_merge($transitiveProd, $filteredProdWithCompatible));
                $allDev = array_unique(array_merge($transitiveDev, $filteredDevWithCompatible));

                if (!empty($allProd)) {
                    $commandsList[] = "composer require --with-all-dependencies " . implode(' ', $allProd);
                }
                if (!empty($allDev)) {
                    $commandsList[] = "composer require --dev --with-all-dependencies " . implode(' ', $allDev);
                }
            } else {
                // No filtered packages, just add transitive dependencies
                $transitiveProdCommand = Utils::buildComposerCommand($transitiveProd, false);
                if ($transitiveProdCommand !== null) {
                    $commandsList[] = $transitiveProdCommand;
                }
                $transitiveDevCommand = Utils::buildComposerCommand($transitiveDev, true);
                if ($transitiveDevCommand !== null) {
                    $commandsList[] = $transitiveDevCommand;
                }
            }
        } else {
            // No transitive updates, but we might have filtered packages that could be installed together
            // Sometimes Composer can resolve conflicts better when multiple packages are installed together
            // Group filtered packages and suggest commands to try installing them together
            if (!empty($filteredByDependenciesProd) || !empty($filteredByDependenciesDev)) {
                // Add packages with compatible dependents to the filtered lists
                $filteredProdWithCompatible = $filteredByDependenciesProd;
                $filteredDevWithCompatible = $filteredByDependenciesDev;

                foreach ($packagesWithCompatibleDependents as $packageString => $hasCompatible) {
                    if ($hasCompatible) {
                        $packageName = explode(':', $packageString)[0];
                        if (isset($devSet[$packageName])) {
                            if (!in_array($packageString, $filteredDevWithCompatible)) {
                                $filteredDevWithCompatible[] = $packageString;
                            }
                        } else {
                            if (!in_array($packageString, $filteredProdWithCompatible)) {
                                $filteredProdWithCompatible[] = $packageString;
                            }
                        }
                    }
                }

                // Suggest grouped commands for filtered packages
                // Composer might be able to resolve conflicts when packages are installed together
                // Include packages that passed dependency checks in the grouped command
                // Installing filtered packages together with packages that passed checks can help resolve conflicts
                $allProdForGrouping = array_unique(array_merge($filteredProdWithCompatible, $prod));
                $allDevForGrouping = array_unique(array_merge($filteredDevWithCompatible, $dev));

                // Check if installed packages need updates to support grouped packages
                $additionalUpdatesForGrouped = [];
                $packagesNeedingMaintainerUpdate = [];
                if (!empty($allProdForGrouping) && !empty($allInstalledPackages)) {
                    VersionResolver::findCompatibleInstalledVersionsForGrouped($allProdForGrouping, $allInstalledPackages, $additionalUpdatesForGrouped, $packagesNeedingMaintainerUpdate, $debug);
                }
                if (!empty($allDevForGrouping) && !empty($allInstalledPackages)) {
                    VersionResolver::findCompatibleInstalledVersionsForGrouped($allDevForGrouping, $allInstalledPackages, $additionalUpdatesForGrouped, $packagesNeedingMaintainerUpdate, $debug);
                }

                // Get maintainer info for packages that need updates
                if (!empty($packagesNeedingMaintainerUpdate) && $groupedMaintainerContacts !== null) {
                    foreach ($packagesNeedingMaintainerUpdate as $pkgName => $info) {
                        $maintainerInfo = MaintainerContactFinder::getMaintainerInfo($pkgName, $debug);
                        if (!empty($maintainerInfo['maintainers']) || !empty($maintainerInfo['repository_url'])) {
                            $groupedMaintainerContacts[$pkgName] = array_merge($maintainerInfo, [
                                'conflict_info' => $info
                            ]);
                        }
                    }
                }

                // Add compatible installed package updates to grouped commands
                if (!empty($additionalUpdatesForGrouped)) {
                    foreach ($additionalUpdatesForGrouped as $pkgName => $info) {
                        $pkgString = $pkgName . ':' . $info['suggested_version'];
                        if (isset($devSet[$pkgName])) {
                            if (!in_array($pkgString, $allDevForGrouping)) {
                                $allDevForGrouping[] = $pkgString;
                                if ($debug) {
                                    error_log("DEBUG: Adding compatible installed package update {$pkgString} to grouped dev command");
                                }
                            }
                        } else {
                            if (!in_array($pkgString, $allProdForGrouping)) {
                                $allProdForGrouping[] = $pkgString;
                                if ($debug) {
                                    error_log("DEBUG: Adding compatible installed package update {$pkgString} to grouped prod command");
                                }
                            }
                        }
                    }
                }

                if (!empty($filteredProdWithCompatible) && count($allProdForGrouping) > 1) {
                    $commandsList[] = "composer require --with-all-dependencies " . implode(' ', $allProdForGrouping);
                    if ($hasGroupedFilteredCommands !== null) {
                        $hasGroupedFilteredCommands['prod'] = true;
                    }
                    if ($debug) {
                        $additionalCount = count($additionalUpdatesForGrouped);
                        error_log("DEBUG: Suggesting grouped command for " . count($allProdForGrouping) . " prod packages (" . count($filteredProdWithCompatible) . " filtered + " . count($prod) . " passed checks" . ($additionalCount > 0 ? " + {$additionalCount} compatible installed updates" : "") . ")");
                    }
                }
                if (!empty($filteredDevWithCompatible) && count($allDevForGrouping) > 1) {
                    $commandsList[] = "composer require --dev --with-all-dependencies " . implode(' ', $allDevForGrouping);
                    if ($hasGroupedFilteredCommands !== null) {
                        $hasGroupedFilteredCommands['dev'] = true;
                    }
                    if ($debug) {
                        $additionalCount = count($additionalUpdatesForGrouped);
                        error_log("DEBUG: Suggesting grouped command for " . count($allDevForGrouping) . " dev packages (" . count($filteredDevWithCompatible) . " filtered + " . count($dev) . " passed checks" . ($additionalCount > 0 ? " + {$additionalCount} compatible installed updates" : "") . ")");
                    }
                }
            }
        }

        return $commandsList;
    }

    /**
     * Format commands output
     *
     * @param array $commandsList Commands list
     * @param array $prod Production packages
     * @param array $dev Development packages
     * @param array $requiredTransitiveUpdates Required transitive updates
     * @param array $allOutdatedProd All outdated production packages
     * @param array $allOutdatedDev All outdated development packages
     * @param array $filteredByDependenciesProd Filtered production packages
     * @param array $filteredByDependenciesDev Filtered development packages
     * @param array $ignoredProd Ignored production packages
     * @param array $ignoredDev Ignored development packages
     * @param string|null $detectedLang Detected language
     * @param bool $debug Debug mode
     * @return array Output lines for commands
     */
    private static function formatCommandsOutput(array $commandsList, array $prod, array $dev, array $requiredTransitiveUpdates, array $allOutdatedProd, array $allOutdatedDev, array $filteredByDependenciesProd, array $filteredByDependenciesDev, array $ignoredProd, array $ignoredDev, ?string $detectedLang, bool $debug, array $data = []): array
    {
        $output = [];

        if (empty($commandsList)) {
            // Determine the reason why there are no packages to update
            $hasOutdated = !empty($allOutdatedProd) || !empty($allOutdatedDev);
            $hasFiltered = !empty($filteredByDependenciesProd) || !empty($filteredByDependenciesDev);
            $hasIgnored = !empty($ignoredProd) || !empty($ignoredDev);

            if (!$hasOutdated) {
                // No outdated packages at all
                $msg = function_exists('t') ? t('no_packages_update', [], $detectedLang) . ' (' . t('all_up_to_date', [], $detectedLang) . ').' : 'No packages to update (all packages are up to date).';
                $output[] = " " . E_OK . "  " . $msg;
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'no_packages_update': " . ($msg));
                }
            } elseif ($hasFiltered && !$hasIgnored) {
                // All outdated packages are filtered by dependency conflicts
                $msg = function_exists('t') ? t('no_packages_update', [], $detectedLang) . ' (' . t('all_have_conflicts', [], $detectedLang) . ').' : 'No packages to update (all outdated packages have dependency conflicts).';
                $output[] = " " . E_OK . "  " . $msg;
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'all_have_conflicts': " . ($msg));
                }
            } elseif ($hasIgnored && !$hasFiltered) {
                // All outdated packages are ignored
                $msg = function_exists('t') ? t('no_packages_update', [], $detectedLang) . ' (' . t('all_ignored', [], $detectedLang) . ').' : 'No packages to update (all outdated packages are ignored).';
                $output[] = " " . E_OK . "  " . $msg;
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'all_ignored': " . ($msg));
                }
            } elseif ($hasFiltered && $hasIgnored) {
                // Mix of filtered and ignored
                $msg = function_exists('t') ? t('no_packages_update', [], $detectedLang) . ' (' . t('all_ignored_or_conflicts', [], $detectedLang) . ').' : 'No packages to update (all outdated packages are ignored or have dependency conflicts).';
                $output[] = " " . E_OK . "  " . $msg;
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'all_ignored_or_conflicts': " . ($msg));
                }
            } else {
                // Fallback (shouldn't happen, but just in case)
                $msg = function_exists('t') ? t('no_packages_update', [], $detectedLang) . '.' : 'No packages to update.';
                $output[] = " " . E_OK . "  " . $msg;
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'no_packages_update' (fallback): " . ($msg));
                }
            }
        } else {
            // Check if we only have transitive dependency commands
            $hasDirectUpdates = !empty($prod) || !empty($dev);
            $hasTransitiveUpdates = !empty($requiredTransitiveUpdates);

            if ($hasDirectUpdates && $hasTransitiveUpdates) {
                $msg = function_exists('t') ? t('suggested_commands', [], $detectedLang) : 'Suggested commands:';
                $output[] = " " . E_WRENCH . "  " . $msg;
                $msg2 = function_exists('t') ? t('includes_transitive', [], $detectedLang) : '(Includes transitive dependencies needed to resolve conflicts)';
                $output[] = "  " . $msg2;
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'suggested_commands': " . $msg);
                }
            } elseif ($hasTransitiveUpdates && !$hasDirectUpdates) {
                $msg = function_exists('t') ? t('suggested_commands_conflicts', [], $detectedLang) : 'Suggested commands to resolve dependency conflicts:';
                $output[] = " " . E_WRENCH . "  " . $msg;
                $msg2 = function_exists('t') ? t('update_transitive_first', [], $detectedLang) : '(Update these transitive dependencies first, then retry updating the filtered packages)';
                $output[] = "  " . $msg2;
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'suggested_commands_conflicts': " . $msg);
                }
            } elseif (!empty($hasGroupedFilteredCommands)) {
                // Commands for grouped filtered packages (no transitive updates)
                $msg = function_exists('t') ? t('suggested_commands_grouped', [], $detectedLang) : 'Suggested commands (try installing together - Composer may resolve conflicts better):';
                $output[] = " " . E_WRENCH . "  " . $msg;
                $msg2 = function_exists('t') ? t('grouped_install_explanation', [], $detectedLang) : '(Installing multiple packages together sometimes helps Composer resolve conflicts)';
                $output[] = "  " . $msg2;

                // Get grouped maintainer contacts from data if available
                $groupedMaintainerContacts = $data['groupedMaintainerContacts'] ?? [];

                if (!empty($groupedMaintainerContacts)) {
                    $msg4 = function_exists('t') ? t('grouped_install_maintainer_needed', [], $detectedLang) : 'Some installed packages need updates from their maintainers:';
                    $output[] = "  " . E_INFO . " " . $msg4;

                    foreach ($groupedMaintainerContacts as $pkgName => $contactInfo) {
                        $conflictInfo = $contactInfo['conflict_info'] ?? [];
                        $requiredBy = !empty($conflictInfo['required_by']) ? implode(', ', array_unique($conflictInfo['required_by'])) : 'grouped packages';
                        $constraints = !empty($conflictInfo['required_constraints']) ? implode(' OR ', $conflictInfo['required_constraints']) : '';

                        $pkgMsg = function_exists('t') ? t('package_needs_update', ['package' => $pkgName, 'requiredBy' => $requiredBy, 'constraints' => $constraints], $detectedLang) :
                            "     - {$pkgName}: Needs update to support {$requiredBy} (requires: {$constraints})";
                        $output[] = $pkgMsg;

                        // Show maintainer contact info
                        if (!empty($contactInfo['maintainers'])) {
                            $maintainersMsg = function_exists('t') ? t('contact_maintainers', [], $detectedLang) : 'Contact maintainer(s):';
                            $output[] = "       " . E_BULB . " {$maintainersMsg}";
                            foreach ($contactInfo['maintainers'] as $maintainer) {
                                $maintainerLine = "          - " . ($maintainer['name'] ?? 'Unknown');
                                if (!empty($maintainer['email'])) {
                                    $maintainerLine .= " ({$maintainer['email']})";
                                }
                                if (!empty($maintainer['homepage'])) {
                                    $maintainerLine .= " - {$maintainer['homepage']}";
                                }
                                $output[] = $maintainerLine;
                            }
                        }

                        // Show repository issue URL
                        if (!empty($contactInfo['repository_url']) && !empty($contactInfo['repository_type'])) {
                            $issueUrl = MaintainerContactFinder::generateIssueUrl($contactInfo['repository_url'], $contactInfo['repository_type']);
                            if ($issueUrl) {
                                $issueMsg = function_exists('t') ? t('create_issue', [], $detectedLang) : 'Create issue:';
                                $output[] = "       " . E_LINK . " {$issueMsg} {$issueUrl}";
                            }
                        }
                    }
                    $output[] = "";
                } else {
                    $msg3 = function_exists('t') ? t('grouped_install_warning', [], $detectedLang) : '(Note: This may still fail if there are conflicts with installed packages that cannot be updated)';
                    $output[] = "  " . E_WARNING . " " . $msg3;
                }

                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'suggested_commands_grouped': " . $msg);
                }
            } else {
                $msg = function_exists('t') ? t('suggested_commands', [], $detectedLang) : 'Suggested commands:';
                $output[] = " " . E_WRENCH . "  " . $msg;
                if ($debug) {
                    error_log("DEBUG: i18n - Using translation for 'suggested_commands' (simple): " . $msg);
                }
            }

            // Add copy hint message
            $copyHint = function_exists('t') ? t('copy_command_hint', [], $detectedLang) : '(Click to copy or select the command)';
            $output[] = "  " . E_INFO . " " . $copyHint;
            $output[] = "";

            foreach ($commandsList as $cmd) {
                // Create clickable link using OSC 8 (supported by many modern terminals)
                // Format: \033]8;;<url>\033\\<text>\033]8;;\033\\
                // For terminal commands, we use a special protocol or just make it selectable
                $clickableCmd = self::makeCommandClickable($cmd);
                $output[] = "  📋 " . $clickableCmd;
            }

            // Add special markers for command extraction (for --run flag)
            $output[] = "---COMMANDS_START---";
            foreach ($commandsList as $cmd) {
                $output[] = $cmd;
            }
            $output[] = "---COMMANDS_END---";
        }

        return $output;
    }

    /**
     * Make a command clickable/copyable in terminal using ANSI escape codes
     * Uses OSC 8 hyperlink protocol for modern terminals
     *
     * @param string $cmd Command to make clickable
     * @return string Command with ANSI escape codes for clickable link
     */
    private static function makeCommandClickable(string $cmd): string
    {
        // Use OSC 8 hyperlink protocol for modern terminals
        // Supported by: iTerm2, Terminal.app, Windows Terminal, VS Code, GNOME Terminal, Konsole, etc.
        // Format: \033]8;;<uri>\033\\<text>\033]8;;\033\\

        // For terminal commands, we create a clickable link
        // The command itself is the "URL" - clicking will select it for copying
        // Some terminals support special protocols, but for maximum compatibility
        // we'll use a data URI or just make it visually distinct

        // Check if we should use hyperlinks (can be controlled by env var)
        $useHyperlinks = getenv('ENABLE_CLICKABLE_COMMANDS') !== 'false';

        if ($useHyperlinks) {
            // Create hyperlink using OSC 8
            // We use the command as both the URI and the text
            // This makes it clickable in supported terminals
            $escapedCmd = rawurlencode($cmd);
            // Use a simple scheme that terminals can handle
            $hyperlink = "\033]8;;{$escapedCmd}\033\\{$cmd}\033]8;;\033\\";
            return $hyperlink;
        }

        // Fallback: just return the command (icon already indicates it's copyable)
        return $cmd;
    }

    /**
     * Format release information output
     *
     * @param array $releaseInfo Release information array
     * @param bool $showReleaseDetail Show detailed release information
     * @return array Output lines for release information
     */
    private static function formatReleaseInfo(array $releaseInfo, bool $showReleaseDetail): array
    {
        $output = [];
        $output[] = "";
        $output[] = E_CLIPBOARD . "  Release information:";

        foreach ($releaseInfo as $pkgName => $info) {
            $output[] = "  " . E_PACKAGE . "  " . $pkgName;

            if (!empty($info['url'])) {
                $output[] = "     " . E_LINK . "  Release: " . $info['url'];
            }

            // Extract changelog link (GitHub releases page)
            $changelogUrl = "";
            if (!empty($info['url'])) {
                $changelogUrl = str_replace('/releases/tag/', '/releases', $info['url']);
                if ($changelogUrl !== $info['url']) {
                    $output[] = "     " . E_MEMO . "  Changelog: " . $changelogUrl;
                }
            }

            // Show detailed information if --release-detail flag is set
            if ($showReleaseDetail) {
                if (!empty($info['name']) && $info['name'] !== $pkgName) {
                    $output[] = "     " . E_CLIPBOARD . "  " . $info['name'];
                }
                if (!empty($info['body'])) {
                    $output[] = "     ──────────────────────────────────────";
                    $bodyLines = explode("\n", $info['body']);
                    foreach ($bodyLines as $line) {
                        $output[] = "     " . $line;
                    }
                    $output[] = "";
                    $output[] = "     ──────────────────────────────────────";
                }
            }
            $output[] = "";
        }

        return $output;
    }
}
