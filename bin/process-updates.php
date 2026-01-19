<?php

declare(strict_types=1);

/**
 * Process outdated packages and generate composer require commands.
 * This file contains all the complex logic and is located in vendor.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 *
 * @see    https://github.com/HecFranco
 */

// Load library classes
$libPaths = [
    __DIR__ . '/lib/autoload.php',  // Development or when in vendor/nowo-tech/composer-update-helper/bin/
    dirname(__DIR__) . '/bin/lib/autoload.php',  // Alternative vendor path
    dirname(dirname(__DIR__)) . '/nowo-tech/composer-update-helper/bin/lib/autoload.php',  // From project root
];

$libLoaded = false;
foreach ($libPaths as $libPath) {
    if (file_exists($libPath)) {
        require_once $libPath;
        $libLoaded = true;
        break;
    }
}

// Load i18n translation functions
// Try multiple paths: vendor location, development location, and relative to this file
$i18nLoaderPaths = [
    __DIR__ . '/i18n/loader.php',  // Development or when in vendor/nowo-tech/composer-update-helper/bin/
    dirname(__DIR__) . '/bin/i18n/loader.php',  // Alternative vendor path
    dirname(dirname(__DIR__)) . '/nowo-tech/composer-update-helper/bin/i18n/loader.php',  // From project root
];

$i18nLoaderLoaded = false;
foreach ($i18nLoaderPaths as $i18nLoaderPath) {
    if (file_exists($i18nLoaderPath)) {
        require_once $i18nLoaderPath;
        $i18nLoaderLoaded = true;
        break;
    }
}

// Fallback: if loader not found, define a dummy t() function
if (!$i18nLoaderLoaded && !function_exists('t')) {
    function t(string $key, array $params = [], ?string $lang = null): string {
        return $key;
    }
    function detectLanguage(): string {
        return 'en';
    }
    function loadTranslations(string $lang): array {
        return [];
    }
}

$raw = getenv('OUTDATED_JSON') ?: '';
// In case some noise got in, try to isolate the first valid JSON:
$start = strpos($raw, '{');
$end   = strrpos($raw, '}');
if ($start === false || $end === false || $end < $start) {
    // Nothing parseable
    exit(0);
}
$json = substr($raw, $start, $end - $start + 1);
$report = json_decode($json, true);
if (!$report || empty($report['installed'])) {
    exit(0);
}

$composer = json_decode(file_get_contents('composer.json'), true);
$require    = $composer['require']     ?? [];
$requireDev = $composer['require-dev'] ?? [];
$allDeps = array_merge($require, $requireDev);
$devSet = array_fill_keys(array_keys($requireDev), true);

// Load library classes - functions are now in classes
// readPackagesFromYaml, readConfigValue, readPackagesFromTxt are in ConfigLoader
// debugLog, normalizeVersion, formatPackageList, addPackageToArray, buildComposerCommand are in Utils

// Load ignored and included packages from YAML or TXT file
$configFile = getenv('CONFIG_FILE') ?: '';
$ignoredPackages = [];
$includedPackages = [];
$checkDependencies = true; // Default: enabled

if ($configFile) {
    // YAML file provided
    if (strpos($configFile, '.yaml') !== false || strpos($configFile, '.yml') !== false) {
        $ignoredPackages = ConfigLoader::readPackagesFromYaml($configFile, 'ignore');
        $includedPackages = ConfigLoader::readPackagesFromYaml($configFile, 'include');
        $checkDependencies = ConfigLoader::readConfigValue($configFile, 'check-dependencies', true);
    } elseif (strpos($configFile, '.txt') !== false) {
        // TXT file (backward compatibility)
        $ignoredPackages = ConfigLoader::readPackagesFromTxt($configFile);
    }
} else {
    // Fallback: try to find config file in current directory
    $yamlFile = null;
    if (file_exists('generate-composer-require.yaml')) {
        $yamlFile = 'generate-composer-require.yaml';
    } elseif (file_exists('generate-composer-require.yml')) {
        $yamlFile = 'generate-composer-require.yml';
    } elseif (file_exists('generate-composer-require.ignore.txt')) {
        $ignoredPackages = ConfigLoader::readPackagesFromTxt('generate-composer-require.ignore.txt');
    }

    if ($yamlFile) {
        $ignoredPackages = ConfigLoader::readPackagesFromYaml($yamlFile, 'ignore');
        $includedPackages = ConfigLoader::readPackagesFromYaml($yamlFile, 'include');
        $checkDependencies = ConfigLoader::readConfigValue($yamlFile, 'check-dependencies', true);
    }
}

// Convert to associative arrays for faster lookup
$ignoredPackages = array_flip($ignoredPackages);
$includedPackages = array_flip($includedPackages);

// Check if release info should be skipped
$showReleaseInfo = getenv('SHOW_RELEASE_INFO') === 'true';
$showReleaseDetail = getenv('SHOW_RELEASE_DETAIL') === 'true';
$showImpactAnalysis = getenv('SHOW_IMPACT_ANALYSIS') === 'true';
$saveImpactToFile = getenv('SAVE_IMPACT_TO_FILE') === 'true';
// If save-impact-to-file is enabled, automatically enable show-impact-analysis (like --save-impact flag does)
if ($saveImpactToFile) {
    $showImpactAnalysis = true;
}
$debug = getenv('DEBUG') === 'true';
$verbose = getenv('VERBOSE') === 'true';

// Detect and load language for translations
$detectedLang = null;
if (function_exists('detectLanguage')) {
    // Try to get from config file first
    $configFileForLang = $configFile ?: (file_exists('generate-composer-require.yaml') ? 'generate-composer-require.yaml' : (file_exists('generate-composer-require.yml') ? 'generate-composer-require.yml' : ''));
    if ($configFileForLang && file_exists($configFileForLang)) {
        $detectedLang = ConfigLoader::readConfigValue($configFileForLang, 'language');
        if ($debug) {
            error_log("DEBUG: i18n - Language from config file: " . ($detectedLang ?: 'not set'));
        }
    }

    // If not in config, detect from system
    if (empty($detectedLang)) {
        $detectedLang = detectLanguage();
        if ($debug) {
            error_log("DEBUG: i18n - Language detected from system: " . $detectedLang);
        }
    }

    if ($debug) {
        error_log("DEBUG: i18n - Final detected language: " . ($detectedLang ?: 'not detected'));
        error_log("DEBUG: i18n - Config file used: " . ($configFileForLang ?: 'not found'));
        error_log("DEBUG: i18n - Translation function available: " . (function_exists('t') ? 'yes' : 'no'));
        error_log("DEBUG: i18n - LoadTranslations function available: " . (function_exists('loadTranslations') ? 'yes' : 'no'));
        error_log("DEBUG: i18n - i18n loader loaded: " . ($i18nLoaderLoaded ? 'yes' : 'no'));
        if (function_exists('loadTranslations')) {
            $testTranslations = loadTranslations($detectedLang ?: 'en');
            error_log("DEBUG: i18n - Loaded translations count: " . count($testTranslations));
            if (count($testTranslations) > 0) {
                error_log("DEBUG: i18n - Sample translation keys: " . implode(', ', array_slice(array_keys($testTranslations), 0, 5)));
                error_log("DEBUG: i18n - Test translation 'no_packages_update': " . t('no_packages_update', [], $detectedLang));
                error_log("DEBUG: i18n - Test translation 'suggested_commands': " . t('suggested_commands', [], $detectedLang));
            } else {
                error_log("DEBUG: i18n - WARNING: No translations loaded! Check i18n file paths.");
            }
        }
    }
} else {
    if ($debug) {
        error_log("DEBUG: i18n - WARNING: detectLanguage() function not available! i18n loader may not have loaded correctly.");
        error_log("DEBUG: i18n - Tried paths: " . implode(', ', $i18nLoaderPaths));
    }
}

// Emoji constants for output formatting
if (!defined('E_OK')) {
    define('E_OK', '✅');
    define('E_WRENCH', '🔧');
    define('E_CLIPBOARD', '📋');
    define('E_PACKAGE', '📦');
    define('E_LINK', '🔗');
    define('E_MEMO', '📝');
    define('E_SKIP', '⏭️');
    define('E_WARNING', '⚠️');
    define('E_BULB', '💡');
    define('E_INFO', 'ℹ️');
}

// String constants
define('DEBUG_PREFIX', 'DEBUG: ');
define('LABEL_PROD', '(prod)');
define('LABEL_DEV', '(dev)');
define('LABEL_NONE', '(none)');
define('COMPOSER_REQUIRE', 'composer require');
define('COMPOSER_REQUIRE_DEV', 'composer require --dev');
define('COMPOSER_REQUIRE_FLAGS', '--with-all-dependencies');

// Helper functions are now in Utils class
// debugLog, normalizeVersion, formatPackageList, addPackageToArray, buildComposerCommand

if ($debug) {
    Utils::debugLog("showReleaseInfo = " . ($showReleaseInfo ? 'true' : 'false'), $debug);
    Utils::debugLog("checkDependencies = " . ($checkDependencies ? 'true' : 'false'), $debug);
    Utils::debugLog("ignoredPackages count = " . count($ignoredPackages), $debug);
    Utils::debugLog("includedPackages count = " . count($includedPackages), $debug);
    if (count($ignoredPackages) > 0) {
        Utils::debugLog("ignoredPackages list: " . implode(', ', array_keys($ignoredPackages)), $debug);
    }
    if (count($includedPackages) > 0) {
        Utils::debugLog("includedPackages list: " . implode(', ', array_keys($includedPackages)), $debug);
    }
    Utils::debugLog("Total outdated packages: " . count($report['installed']), $debug);
    Utils::debugLog("require packages: " . count($require), $debug);
    Utils::debugLog("require-dev packages: " . count($requireDev), $debug);
}

// ============================================================================
// FRAMEWORK DETECTION AND CONSTRAINTS
// ============================================================================

// Framework detection is now in FrameworkDetector class
$frameworkConstraints = FrameworkDetector::detectFrameworkConstraints($composer, $allDeps, $debug);

// Functions moved to classes - use wrapper functions for backward compatibility during refactoring
function versionSatisfiesConstraint($version, $constraint) {
    return DependencyAnalyzer::versionSatisfiesConstraint($version, $constraint);
}

function getInstalledPackageVersion($packageName) {
    return DependencyAnalyzer::getInstalledPackageVersion($packageName);
}

function getPackageRequirements($packageName, $version) {
    return PackageInfoProvider::getPackageRequirements($packageName, $version);
}

function getPackageRequirementsFromComposer($packageName, $version) {
    return PackageInfoProvider::getPackageRequirementsFromComposer($packageName, $version);
}

// Function to find the highest compatible version considering dependent packages
// NOTE: This is a wrapper for VersionResolver::findCompatibleVersion
function findCompatibleVersion($packageName, $proposedVersion, $debug = false, $checkDependencies = true, ?array &$requiredTransitiveUpdates = null, ?array &$conflictingDependents = null) {
    return VersionResolver::findCompatibleVersion(
        $packageName,
        $proposedVersion,
        $debug,
        $checkDependencies,
        $requiredTransitiveUpdates,
        $conflictingDependents
    );
}

// Legacy function removed - code is now in VersionResolver class

// Function moved to FrameworkDetector class
function getLatestVersionInConstraint($packageName, $baseVersion) {
    return FrameworkDetector::getLatestVersionInConstraint($packageName, $baseVersion);
}

function shouldLimitVersion($packageName, $latestVersion, $frameworkConstraints) {
    return FrameworkDetector::shouldLimitVersion($packageName, $latestVersion, $frameworkConstraints);
}

function getFrameworkConstraint($packageName, $frameworkConstraints) {
    return FrameworkDetector::getFrameworkConstraint($packageName, $frameworkConstraints);
}

function normalizeVersion($version) {
    return Utils::normalizeVersion($version);
}

function addPackageToArray($name, $constraint, $devSet, &$prod, &$dev, $debug = false) {
    return Utils::addPackageToArray($name, $constraint, $devSet, $prod, $dev, $debug);
}

function buildComposerCommand($packages, $isDev = false) {
    return Utils::buildComposerCommand($packages, $isDev);
}

function formatPackageList($packages, $label, $indent = '     ') {
    return Utils::formatPackageList($packages, $label, $indent);
}

// Functions moved to classes - using wrappers for backward compatibility
function isPackageAbandoned($packageName, $debug = false) {
    return AbandonedPackageDetector::isPackageAbandoned($packageName, $debug);
}

function findFallbackVersion($packageName, $targetVersion, $conflictingDependents, $debug = false) {
    return FallbackVersionFinder::findFallbackVersion($packageName, $targetVersion, $conflictingDependents, $debug);
}

function findAlternativePackages($packageName, $debug = false) {
    return AlternativePackageFinder::findAlternatives($packageName, $debug);
}

function getGitHubRepoFromPackagist($packageName) {
    return PackageInfoProvider::getGitHubRepoFromPackagist($packageName);
}

function getReleaseInfo($githubRepo, $version) {
    return PackageInfoProvider::getReleaseInfo($githubRepo, $version);
}

// ============================================================================
// PROCESS PACKAGES
// ============================================================================

$prod = [];
$dev  = [];
$ignoredProd = [];
$ignoredDev  = [];
$releaseInfo = []; // Store release information for packages

// Track all outdated packages (before dependency checking) for debug output
$allOutdatedProd = [];
$allOutdatedDev  = [];
$filteredByDependenciesProd = [];
$filteredByDependenciesDev  = [];
// Track which dependent packages cause conflicts for filtered packages
$filteredPackageConflicts = []; // Format: 'package:version' => ['dependent1' => 'constraint1', 'dependent2' => 'constraint2']
// Store abandoned package info for filtered packages (format: 'package:version' => ['abandoned' => true, 'replacement' => 'new/package'])
$filteredPackageAbandoned = []; // Format: 'package:version' => ['abandoned' => true, 'replacement' => 'new/package'|null]
// Store fallback versions for filtered packages (format: 'package:version' => 'fallbackVersion')
$filteredPackageFallbacks = []; // Format: 'package:version' => 'fallbackVersion'
// Store alternative packages for filtered packages (format: 'package:version' => ['alternatives' => [...], 'reason' => '...'])
$filteredPackageAlternatives = []; // Format: 'package:version' => ['alternatives' => [...], 'reason' => '...']
$filteredPackageMaintainerContacts = []; // Format: 'package:version' => ['maintainers' => [...], 'repository_url' => '...', 'is_stale' => bool, ...]
// Store impact analysis for filtered packages (format: 'package:version' => ['direct' => [...], 'transitive' => [...]])
$filteredPackageImpact = []; // Format: 'package:version' => ['direct' => [...], 'transitive' => [...], 'total_affected' => int]
// Track transitive dependencies that need updates to resolve conflicts
$requiredTransitiveUpdates = [];

foreach ($report['installed'] as $pkg) {
    if (!isset($pkg['name'])) continue;
    $name   = $pkg['name'];
    $installed = $pkg['version'] ?? null;
    $latest = $pkg['latest'] ?? null;

    if ($debug) {
        error_log("DEBUG: Processing package: {$name} (installed: {$installed}, latest: {$latest})");
    }

    // Check if package is included (force include even if ignored)
    $isIncluded = isset($includedPackages[$name]);
    $isIgnored = isset($ignoredPackages[$name]);

    if ($debug) {
        error_log("DEBUG:   - isIgnored: " . ($isIgnored ? 'true' : 'false'));
        error_log("DEBUG:   - isIncluded: " . ($isIncluded ? 'true' : 'false'));
    }

    // Check if package is ignored (unless it's explicitly included)
    if ($isIgnored && !$isIncluded) {
        if ($debug) {
            error_log("DEBUG:   - Action: IGNORED (in ignore list and not in include list)");
        }
        if ($latest) {
            $normalized = normalizeVersion($latest);
            if (isset($devSet[$name])) {
                $ignoredDev[] = $name . ':' . $normalized;
            } else {
                $ignoredProd[] = $name . ':' . $normalized;
            }
        }
        continue;
    }

    if ($isIncluded && $debug) {
        error_log("DEBUG:   - Action: INCLUDED (forced include, overriding ignore)");
    }

    if (!$latest) {
        if ($debug) {
            error_log("DEBUG:   - Action: SKIPPED (no latest version available)");
        }
        continue;
    }

    $normalized = normalizeVersion($latest);
    $installedNormalized = normalizeVersion($installed);

    // Check if this package belongs to a framework and should be limited
    if (shouldLimitVersion($name, $latest, $frameworkConstraints)) {
        $frameworkBase = getFrameworkConstraint($name, $frameworkConstraints);
        $specificVersion = getLatestVersionInConstraint($name, $frameworkBase);
        if ($specificVersion) {
            $constraint = $specificVersion;
        } else {
            // Fallback: use the base version with wildcard
            $constraint = $frameworkBase . '.*';
        }
    } else {
        $constraint = $normalized;
    }

    // Compare installed version with the proposed one: only include if there's really an update
    $needsUpdate = false;
    if ($installedNormalized) {
        $constraintNormalized = $constraint;
        // If it's a wildcard constraint, we can't compare directly, so we include it
        if (strpos($constraint, '*') === false && strpos($constraint, '^') === false && strpos($constraint, '~') === false) {
            // It's a specific version, we can compare
            $comparison = version_compare($installedNormalized, $constraintNormalized, '>=');
            if ($debug) {
                error_log("DEBUG:   - Version comparison: {$installedNormalized} >= {$constraintNormalized} = " . ($comparison ? 'true' : 'false'));
            }
            if ($comparison) {
                // Already at that version or higher, don't include
                if ($debug) {
                    error_log("DEBUG:   - Action: SKIPPED (already at or above target version)");
                }
                continue;
            }
            $needsUpdate = true;
        } elseif ($debug) {
            error_log("DEBUG:   - Wildcard constraint, including for update");
            $needsUpdate = true;
        }
    } else {
        $needsUpdate = true;
    }

    // Track all outdated packages (before dependency checking) for comparison
    // Only track if it actually needs an update
    if ($needsUpdate) {
        $packageString = $name . ':' . $constraint;
        if (isset($devSet[$name])) {
            $allOutdatedDev[] = $packageString;
        } else {
            $allOutdatedProd[] = $packageString;
        }

        // Check if package is abandoned (for ALL outdated packages, not just those with conflicts)
        // Show progress message for abandoned package checking (only once)
        $progressMsg = function_exists('t') ? t('checking_abandoned_packages', [], $detectedLang) : '⏳ Checking for abandoned packages...';
        Utils::showProgressMessage('checking_abandoned_packages', $progressMsg, $debug, $verbose);

        $abandonedInfo = isPackageAbandoned($name, $debug);
        if ($abandonedInfo && $abandonedInfo['abandoned']) {
            $filteredPackageAbandoned[$packageString] = $abandonedInfo;
            if ($debug) {
                error_log("DEBUG: Package {$name} is abandoned" .
                          ($abandonedInfo['replacement'] ? " (replacement: {$abandonedInfo['replacement']})" : ""));
            }
        }
    }

    // Check dependency compatibility before suggesting update
    // Now supports wildcard constraints (^, ~, *) using versionSatisfiesConstraint
    if ($needsUpdate && $checkDependencies) {
        // Show progress message for dependency conflict checking (only once)
        $progressMsg = function_exists('t') ? t('checking_dependency_conflicts', [], $detectedLang) : '⏳ Checking dependency conflicts...';
        Utils::showProgressMessage('checking_dependency_conflicts', $progressMsg, $debug, $verbose);

        $conflictingDependents = [];
        $fallbackVersion = null; // New variable for fallback versions
        $compatibleVersion = findCompatibleVersion($name, $constraint, $debug, $checkDependencies, $requiredTransitiveUpdates, $conflictingDependents);

        // If no compatible version but we have conflicts, try fallback
        if ($compatibleVersion === null && !empty($conflictingDependents)) {
            // Show progress message for fallback version search (only once)
            $progressMsg = function_exists('t') ? t('searching_fallback_versions', [], $detectedLang) : '⏳ Searching for fallback versions...';
            Utils::showProgressMessage('searching_fallback_versions', $progressMsg, $debug, $verbose);

            $fallbackVersion = findFallbackVersion($name, $constraint, $conflictingDependents, $debug);
            if ($fallbackVersion) {
                // Verify fallback version also satisfies package requirements
                $fallbackRequirements = getPackageRequirements($name, $fallbackVersion);
                $fallbackHasConflict = false;

                foreach ($fallbackRequirements as $reqPackage => $reqConstraint) {
                    // Skip php and ext-* requirements
                    if ($reqPackage === 'php' || strpos($reqPackage, 'php-') === 0 || strpos($reqPackage, 'ext-') === 0) {
                        continue;
                    }

                    // Check if installed version satisfies requirement
                    $installedVersion = getInstalledPackageVersion($reqPackage);
                    if ($installedVersion && !versionSatisfiesConstraint($installedVersion, $reqConstraint)) {
                        $fallbackHasConflict = true;
                        if ($debug) {
                            error_log("DEBUG: Fallback version {$fallbackVersion} has requirement conflict: {$reqPackage} requires {$reqConstraint}, but installed is {$installedVersion}");
                        }
                        break;
                    }
                }

                if (!$fallbackHasConflict) {
                    // Store fallback for output
                    $filteredPackageFallbacks[$packageString] = $fallbackVersion;
                    if ($debug) {
                        error_log("DEBUG: Found fallback version {$fallbackVersion} for {$packageString}");
                    }
                } else {
                    $fallbackVersion = null; // Fallback has conflicts, don't use it
                }
            }
        }

        if ($compatibleVersion === null) {
            // No compatible version found, skip this update
            if ($debug) {
                error_log("DEBUG:   - Action: SKIPPED (no compatible version found due to dependency constraints)");
                if (!empty($conflictingDependents)) {
                    error_log("DEBUG:   - Conflicting dependents for {$packageString}: " . json_encode($conflictingDependents));
                }
            }
            // Track filtered packages and their conflicts
            if (isset($devSet[$name])) {
                $filteredByDependenciesDev[] = $packageString;
            } else {
                $filteredByDependenciesProd[] = $packageString;
            }
            // Store conflicting dependents for this package
            if (!empty($conflictingDependents)) {
                $filteredPackageConflicts[$packageString] = $conflictingDependents;

                // Analyze impact of updating this package (only if enabled)
                if ($showImpactAnalysis) {
                    $impact = ImpactAnalyzer::analyzeImpact($name, $constraint, $debug);
                    if (!empty($impact['direct']) || !empty($impact['transitive'])) {
                        $formattedImpact = ImpactAnalyzer::formatImpactForOutput($impact, $name, $constraint);
                        $filteredPackageImpact[$packageString] = $formattedImpact;
                        if ($debug) {
                            error_log("DEBUG: Impact analysis for {$packageString}: " .
                                      count($formattedImpact['direct_affected']) . " direct, " .
                                      count($formattedImpact['transitive_affected']) . " transitive");
                        }
                    }
                }

                // If package is abandoned without replacement or no fallback available, search for alternatives
                if (isset($filteredPackageAbandoned[$packageString])) {
                    $abandonedInfo = $filteredPackageAbandoned[$packageString];
                    if (!$abandonedInfo['replacement'] && !isset($filteredPackageFallbacks[$packageString])) {
                        // Show progress message for alternative package search (only once)
                        $progressMsg = function_exists('t') ? t('searching_alternative_packages', [], $detectedLang) : '⏳ Searching for alternative packages...';
                        Utils::showProgressMessage('searching_alternative_packages', $progressMsg, $debug, $verbose);

                        $alternatives = AlternativePackageFinder::findAlternatives($name, $debug);
                        if ($alternatives && !empty($alternatives['alternatives'])) {
                            $filteredPackageAlternatives[$packageString] = $alternatives;
                            if ($debug) {
                                error_log("DEBUG: Found " . count($alternatives['alternatives']) . " alternative(s) for abandoned package {$name}");
                            }
                        }
                    }
                } elseif (!isset($filteredPackageFallbacks[$packageString])) {
                    // If not abandoned but no fallback available, search for alternatives
                    // Show progress message for alternative package search (only once)
                    $progressMsg = function_exists('t') ? t('searching_alternative_packages', [], $detectedLang) : '⏳ Searching for alternative packages...';
                    Utils::showProgressMessage('searching_alternative_packages', $progressMsg, $debug, $verbose);

                    $alternatives = AlternativePackageFinder::findAlternatives($name, $debug);
                    if ($alternatives && !empty($alternatives['alternatives'])) {
                        $filteredPackageAlternatives[$packageString] = $alternatives;
                        if ($debug) {
                            error_log("DEBUG: Found " . count($alternatives['alternatives']) . " alternative(s) for package {$name}");
                        }
                    } else {
                        // No alternatives found - check if we should suggest maintainer contact
                        // Only suggest if we have a conflicting package and constraint
                        if (!empty($conflictingDependents)) {
                            $firstConflictPackage = array_key_first($conflictingDependents);
                            $firstConflictConstraint = $conflictingDependents[$firstConflictPackage];

                            // Get current constraint from packageString (format: "package:constraint")
                            $currentConstraint = explode(':', $packageString, 2)[1] ?? '';

                            // Check if we should suggest maintainer contact
                            if (MaintainerContactFinder::shouldSuggestContact(
                                $name,
                                $firstConflictPackage,
                                $currentConstraint,
                                $firstConflictConstraint,
                                $debug
                            )) {
                                // Show progress message for maintainer info checking (only once)
                                $progressMsg = function_exists('t') ? t('checking_maintainer_info', [], $detectedLang) : '⏳ Checking maintainer information...';
                                Utils::showProgressMessage('checking_maintainer_info', $progressMsg, $debug, $verbose);

                                $maintainerInfo = MaintainerContactFinder::getMaintainerInfo($name, $debug);
                                if (!empty($maintainerInfo['maintainers']) || $maintainerInfo['repository_url']) {
                                    $filteredPackageMaintainerContacts[$packageString] = $maintainerInfo;
                                    if ($debug) {
                                        error_log("DEBUG: Suggesting maintainer contact for package {$name}");
                                    }
                                }
                            }
                        }
                    }
                }

                if ($debug) {
                    error_log("DEBUG:   - Stored conflicts for {$packageString}: " . count($conflictingDependents) . " conflicting dependent(s)");
                }
            }
            continue;
        }
        if ($compatibleVersion !== $constraint) {
            // Found a compatible version that's different from proposed
            if ($debug) {
                error_log("DEBUG:   - Compatible version found: {$compatibleVersion} (proposed was {$constraint})");
            }
            $constraint = $compatibleVersion;
        }
    }

    // Get release information for this package (only for specific versions, not wildcards)
    if ($showReleaseInfo && strpos($constraint, '*') === false && strpos($constraint, '^') === false && strpos($constraint, '~') === false) {
        // Only fetch release info for specific versions to avoid unnecessary API calls
        // Show progress indicator (only if not in debug mode, as debug shows everything)
        if (!$debug && !isset($releaseInfoShown)) {
            error_log("⏳ Fetching release information...");
            $releaseInfoShown = true;
        }
        $githubRepo = getGitHubRepoFromPackagist($name);
        if ($githubRepo) {
            $release = getReleaseInfo($githubRepo, $latest);
            if ($release) {
                $releaseInfo[$name] = $release;
            }
        }
    }

    addPackageToArray($name, $constraint, $devSet, $prod, $dev, $debug);
}

// Show count of outdated packages found (only if not in debug mode and we have packages)
$totalOutdated = count($allOutdatedProd) + count($allOutdatedDev);
if ($totalOutdated > 0 && !$debug) {
    $countMsg = function_exists('t') ? t('found_outdated_packages', [$totalOutdated], $detectedLang) : "✅ Found {$totalOutdated} outdated package(s)";
    error_log($countMsg);
}

// ============================================================================
// CHECK ALL INSTALLED PACKAGES FOR ABANDONED STATUS
// ============================================================================
// Check ALL installed packages (not just outdated ones) for abandoned status
$allInstalledAbandoned = []; // Format: 'package:version' => ['abandoned' => true, 'replacement' => 'new/package'|null, 'is_dev' => bool]

// Show progress message for checking all installed packages (only once)
$progressMsg = function_exists('t') ? t('checking_all_abandoned_packages', [], $detectedLang) : '⏳ Checking all installed packages for abandoned status...';
Utils::showProgressMessage('checking_all_abandoned_packages', $progressMsg, $debug, $verbose);

// Check all packages from composer.json (require and require-dev)
$allInstalledPackages = array_merge(
    array_keys($require),
    array_keys($requireDev)
);

foreach ($allInstalledPackages as $packageName) {
    // Skip if already checked (in filteredPackageAbandoned)
    $alreadyChecked = false;
    foreach ($filteredPackageAbandoned as $packageString => $abandonedInfo) {
        if (strpos($packageString, $packageName . ':') === 0) {
            $alreadyChecked = true;
            break;
        }
    }

    // If already checked in outdated packages, skip to avoid duplicates
    if ($alreadyChecked) {
        continue;
    }

    // Check if package is abandoned
    $abandonedInfo = isPackageAbandoned($packageName, $debug);
    if ($abandonedInfo && $abandonedInfo['abandoned']) {
        // Get installed version from composer.lock or composer.json
        $installedVersion = getInstalledPackageVersion($packageName);
        $packageString = $packageName . ':' . ($installedVersion ?: 'unknown');

        $allInstalledAbandoned[$packageString] = [
            'abandoned' => true,
            'replacement' => $abandonedInfo['replacement'] ?? null,
            'is_dev' => isset($devSet[$packageName])
        ];

        if ($debug) {
            error_log("DEBUG: Installed package {$packageName} is abandoned" .
                      ($abandonedInfo['replacement'] ? " (replacement: {$abandonedInfo['replacement']})" : ""));
        }
    }
}

// ============================================================================
// OUTPUT FORMATTING
// ============================================================================

// Prepare data for OutputFormatter
$outputData = [
    'prod' => $prod,
    'dev' => $dev,
    'ignoredProd' => $ignoredProd,
    'ignoredDev' => $ignoredDev,
    'frameworkConstraints' => $frameworkConstraints,
    'allOutdatedProd' => $allOutdatedProd,
    'allOutdatedDev' => $allOutdatedDev,
    'filteredByDependenciesProd' => $filteredByDependenciesProd,
    'filteredByDependenciesDev' => $filteredByDependenciesDev,
    'filteredPackageConflicts' => $filteredPackageConflicts,
    'filteredPackageAbandoned' => $filteredPackageAbandoned,
    'filteredPackageFallbacks' => $filteredPackageFallbacks,
    'filteredPackageAlternatives' => $filteredPackageAlternatives,
    'filteredPackageMaintainerContacts' => $filteredPackageMaintainerContacts,
    'filteredPackageImpact' => $filteredPackageImpact,
    'requiredTransitiveUpdates' => $requiredTransitiveUpdates,
    'releaseInfo' => $releaseInfo,
    'devSet' => $devSet,
    'allInstalledAbandoned' => $allInstalledAbandoned,
];

$outputOptions = [
    'debug' => $debug,
    'verbose' => $verbose,
    'checkDependencies' => $checkDependencies,
    'showReleaseInfo' => $showReleaseInfo,
    'showReleaseDetail' => $showReleaseDetail,
    'showImpactAnalysis' => $showImpactAnalysis,
    'detectedLang' => $detectedLang,
];

// Generate output using OutputFormatter
$output = OutputFormatter::formatOutput($outputData, $outputOptions);

// Check if there's nothing to show (early exit case)
if (count($output) === 1 && strpos($output[0], 'No outdated direct dependencies') !== false) {
    if ($verbose || $debug) {
        error_log("ℹ️  No outdated direct dependencies found.");
    }
    echo $output[0] . PHP_EOL;
    exit(0);
}

// Save impact analysis to file if requested
if ($saveImpactToFile && !empty($filteredPackageImpact)) {
    $impactFile = 'composer-update-impact.txt';
    $impactContent = [];
    $impactContent[] = "Composer Update Helper - Impact Analysis Report";
    $impactContent[] = "Generated: " . date('Y-m-d H:i:s');
    $impactContent[] = "";
    $impactContent[] = "================================================================================";
    $impactContent[] = "";

    foreach ($filteredPackageImpact as $packageString => $impact) {
        if ($impact['total_affected'] > 0) {
            $packageName = explode(':', $packageString)[0];
            $newVersion = $impact['new_version'];
            $impactContent[] = "Impact Analysis: Updating {$packageName} to {$newVersion}";
            $impactContent[] = str_repeat("-", 80);
            $impactContent[] = "";

            if (!empty($impact['direct_affected'])) {
                $impactContent[] = "Directly Affected Packages (" . count($impact['direct_affected']) . "):";
                foreach ($impact['direct_affected'] as $affected) {
                    $impactContent[] = "  - {$affected['package']} ({$affected['reason']})";
                }
                $impactContent[] = "";
            }

            if (!empty($impact['transitive_affected'])) {
                $impactContent[] = "Transitively Affected Packages (" . count($impact['transitive_affected']) . "):";
                foreach ($impact['transitive_affected'] as $affected) {
                    $impactContent[] = "  - {$affected['package']} ({$affected['reason']})";
                }
                $impactContent[] = "";
            }

            $impactContent[] = "Total Affected: {$impact['total_affected']} package(s)";
            $impactContent[] = "";
            $impactContent[] = str_repeat("=", 80);
            $impactContent[] = "";
        }
    }

    $impactContent[] = "End of Impact Analysis Report";

    if (file_put_contents($impactFile, implode(PHP_EOL, $impactContent))) {
        $saveMsg = function_exists('t') ? t('impact_analysis_saved', [$impactFile], $detectedLang) : "✅ Impact analysis saved to: {$impactFile}";
        error_log($saveMsg);
    } else {
        error_log("⚠️  Failed to save impact analysis to: {$impactFile}");
    }
}

// Debug output summary
if ($debug) {
    error_log("DEBUG: Generated output:");
    error_log("DEBUG:   - Prod packages: " . count($prod) . " (" . implode(', ', $prod) . ")");
    error_log("DEBUG:   - Dev packages: " . count($dev) . " (" . implode(', ', $dev) . ")");
    error_log("DEBUG:   - Ignored prod: " . count($ignoredProd) . " (" . implode(', ', $ignoredProd) . ")");
    error_log("DEBUG:   - Ignored dev: " . count($ignoredDev) . " (" . implode(', ', $ignoredDev) . ")");
}

// Output the formatted result
echo implode(PHP_EOL, $output);

