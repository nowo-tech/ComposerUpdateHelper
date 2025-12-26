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

// Function to read packages from YAML file
function readPackagesFromYaml(string $yamlPath, string $section): array
{
    if (!file_exists($yamlPath)) {
        return [];
    }

    $content = file_get_contents($yamlPath);
    if ($content === false) {
        return [];
    }

    $packages = [];
    $lines = explode("\n", $content);
    $inSection = false;

    foreach ($lines as $line) {
        $trimmedLine = trim($line);
        $originalLine = $line;

        // Skip empty lines and pure comment lines
        if (empty($trimmedLine) || strpos($trimmedLine, '#') === 0) {
            continue;
        }

        // Check for section header
        if (preg_match('/^' . preg_quote($section, '/') . ':\s*$/', $trimmedLine)) {
            $inSection = true;
            continue;
        }

        // Check for other section headers (end current section)
        if ($inSection && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*:\s*$/', $trimmedLine)) {
            $inSection = false;
            continue;
        }

        // Extract packages from current section
        if ($inSection && preg_match('/^\s*-\s+([^#]+)/', $originalLine, $matches)) {
            $package = trim($matches[1]);
            if (!empty($package)) {
                $packages[] = $package;
            }
        }
    }

    return $packages;
}

// Function to read packages from TXT file (backward compatibility)
function readPackagesFromTxt(string $txtPath): array
{
    if (!file_exists($txtPath)) {
        return [];
    }

    $content = file_get_contents($txtPath);
    if ($content === false) {
        return [];
    }

    $packages = [];
    $lines = explode("\n", $content);

    foreach ($lines as $line) {
        $line = trim($line);
        // Skip comments and empty lines
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        if (!empty($line)) {
            $packages[] = $line;
        }
    }

    return $packages;
}

// Load ignored and included packages from YAML or TXT file
$configFile = getenv('CONFIG_FILE') ?: '';
$ignoredPackages = [];
$includedPackages = [];

if ($configFile) {
    // YAML file provided
    if (strpos($configFile, '.yaml') !== false || strpos($configFile, '.yml') !== false) {
        $ignoredPackages = readPackagesFromYaml($configFile, 'ignore');
        $includedPackages = readPackagesFromYaml($configFile, 'include');
    } elseif (strpos($configFile, '.txt') !== false) {
        // TXT file (backward compatibility)
        $ignoredPackages = readPackagesFromTxt($configFile);
    }
} else {
    // Fallback: try to find config file in current directory
    $yamlFile = null;
    if (file_exists('generate-composer-require.yaml')) {
        $yamlFile = 'generate-composer-require.yaml';
    } elseif (file_exists('generate-composer-require.yml')) {
        $yamlFile = 'generate-composer-require.yml';
    } elseif (file_exists('generate-composer-require.ignore.txt')) {
        $ignoredPackages = readPackagesFromTxt('generate-composer-require.ignore.txt');
    }

    if ($yamlFile) {
        $ignoredPackages = readPackagesFromYaml($yamlFile, 'ignore');
        $includedPackages = readPackagesFromYaml($yamlFile, 'include');
    }
}

// Convert to associative arrays for faster lookup
$ignoredPackages = array_flip($ignoredPackages);
$includedPackages = array_flip($includedPackages);

// Check if release info should be skipped
$showReleaseInfo = getenv('SHOW_RELEASE_INFO') === 'true';
$showReleaseDetail = getenv('SHOW_RELEASE_DETAIL') === 'true';
$debug = getenv('DEBUG') === 'true';
$verbose = getenv('VERBOSE') === 'true';

// Emoji constants for output formatting
if (!defined('E_OK')) {
    define('E_OK', '✅');
    define('E_WRENCH', '🔧');
    define('E_CLIPBOARD', '📋');
    define('E_PACKAGE', '📦');
    define('E_LINK', '🔗');
    define('E_MEMO', '📝');
    define('E_SKIP', '⏭️');
}

if ($debug) {
    error_log("DEBUG: showReleaseInfo = " . ($showReleaseInfo ? 'true' : 'false'));
    error_log("DEBUG: ignoredPackages count = " . count($ignoredPackages));
    error_log("DEBUG: includedPackages count = " . count($includedPackages));
    if (count($ignoredPackages) > 0) {
        error_log("DEBUG: ignoredPackages list: " . implode(', ', array_keys($ignoredPackages)));
    }
    if (count($includedPackages) > 0) {
        error_log("DEBUG: includedPackages list: " . implode(', ', array_keys($includedPackages)));
    }
    error_log("DEBUG: Total outdated packages: " . count($report['installed']));
    error_log("DEBUG: require packages: " . count($require));
    error_log("DEBUG: require-dev packages: " . count($requireDev));
}

// ============================================================================
// FRAMEWORK DETECTION AND CONSTRAINTS
// ============================================================================

// Framework configurations: prefix => core package
$frameworkConfigs = [
    'symfony' => [
        'prefix' => 'symfony/',
        'corePackage' => null, // Uses extra.symfony.require
        'extraKey' => ['extra', 'symfony', 'require'],
    ],
    'laravel' => [
        'prefix' => 'laravel/',
        'corePackage' => 'laravel/framework',
        'related' => ['illuminate/'],
    ],
    'yii' => [
        'prefix' => 'yiisoft/',
        'corePackage' => 'yiisoft/yii2',
    ],
    'cakephp' => [
        'prefix' => 'cakephp/',
        'corePackage' => 'cakephp/cakephp',
    ],
    'laminas' => [
        'prefix' => 'laminas/',
        'corePackage' => 'laminas/laminas-mvc',
        'fallbackCore' => 'laminas/laminas-servicemanager',
    ],
    'codeigniter' => [
        'prefix' => 'codeigniter4/',
        'corePackage' => 'codeigniter4/framework',
    ],
    'slim' => [
        'prefix' => 'slim/',
        'corePackage' => 'slim/slim',
    ],
];

// Detected framework constraints (prefix => base version like "7.1")
$frameworkConstraints = [];

// Function to extract the base version from a constraint or version (e.g.: "7.4.*" -> "7.4", "^8.0" -> "8.0")
function extractBaseVersion($constraint) {
    // Remove special characters and get the main numeric part
    $constraint = ltrim($constraint, '^~>=<vV');
    $parts = preg_split('/[^0-9]/', $constraint, 3);
    if (count($parts) >= 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
        return $parts[0] . '.' . $parts[1];
    }
    return null;
}

// Detect Symfony constraint from extra.symfony.require
if (isset($composer['extra']['symfony']['require'])) {
    $baseVersion = extractBaseVersion($composer['extra']['symfony']['require']);
    if ($baseVersion) {
        $frameworkConstraints['symfony/'] = $baseVersion;
        if ($debug) {
            error_log("DEBUG: Detected Symfony constraint: {$baseVersion}.* (from extra.symfony.require)");
        }
    }
}

// Detect other frameworks from installed versions
foreach ($frameworkConfigs as $name => $config) {
    if ($name === 'symfony') continue; // Already handled above

    $prefix = $config['prefix'];
    if (isset($frameworkConstraints[$prefix])) continue;

    // Try core package
    $corePackage = $config['corePackage'] ?? null;
    if ($corePackage && isset($allDeps[$corePackage])) {
        $baseVersion = extractBaseVersion($allDeps[$corePackage]);
        if ($baseVersion) {
            $frameworkConstraints[$prefix] = $baseVersion;
            if ($debug) {
                error_log("DEBUG: Detected {$name} framework constraint: {$baseVersion}.* (from {$corePackage})");
            }
            // Also add related prefixes (e.g., illuminate/ for Laravel)
            if (isset($config['related'])) {
                foreach ($config['related'] as $relatedPrefix) {
                    $frameworkConstraints[$relatedPrefix] = $baseVersion;
                    if ($debug) {
                        error_log("DEBUG: Added related prefix constraint: {$relatedPrefix} = {$baseVersion}.*");
                    }
                }
            }
            continue;
        }
    }

    // Try fallback core package
    $fallbackCore = $config['fallbackCore'] ?? null;
    if ($fallbackCore && isset($allDeps[$fallbackCore])) {
        $baseVersion = extractBaseVersion($allDeps[$fallbackCore]);
        if ($baseVersion) {
            $frameworkConstraints[$prefix] = $baseVersion;
        }
    }
}

// Function to check if a package belongs to a framework and get its constraint
function getFrameworkConstraint($packageName, $frameworkConstraints) {
    foreach ($frameworkConstraints as $prefix => $baseVersion) {
        if (strpos($packageName, $prefix) === 0) {
            return $baseVersion;
        }
    }
    return null;
}

// Function to check if a version exceeds the framework constraint
function shouldLimitVersion($packageName, $latestVersion, $frameworkConstraints) {
    $constraintBase = getFrameworkConstraint($packageName, $frameworkConstraints);
    if (!$constraintBase) {
        return false;
    }

    // Normalize latest version
    $latest = ltrim($latestVersion, 'v');
    $latestBase = extractBaseVersion($latest);
    if (!$latestBase) {
        return false;
    }

    // Compare base versions
    $latestParts = explode('.', $latestBase);
    $baseParts = explode('.', $constraintBase);

    if (count($latestParts) >= 2 && count($baseParts) >= 2) {
        $latestMajor = (int)$latestParts[0];
        $latestMinor = (int)$latestParts[1];
        $baseMajor = (int)$baseParts[0];
        $baseMinor = (int)$baseParts[1];

        // If latest exceeds the constraint
        if ($latestMajor > $baseMajor || ($latestMajor === $baseMajor && $latestMinor > $baseMinor)) {
            return true;
        }
    }

    return false;
}

// Function to get the latest specific version that meets a constraint
function getLatestVersionInConstraint($packageName, $baseVersion) {
    $composerBin = getenv('COMPOSER_BIN') ?: 'composer';
    $phpBin = getenv('PHP_BIN') ?: 'php';

    // Run composer show to get available versions
    $cmd = escapeshellarg($phpBin) . ' -d date.timezone=UTC ' . escapeshellarg($composerBin) .
           ' show ' . escapeshellarg($packageName) . ' --all --format=json 2>/dev/null';

    $output = shell_exec($cmd);
    if (!$output) {
        return null;
    }

    $data = json_decode($output, true);
    if (!$data || !isset($data['versions'])) {
        return null;
    }

    $basePrefix = $baseVersion . '.';

    // Filter versions that start with the prefix and get the latest one
    $matchingVersions = [];
    foreach ($data['versions'] as $version) {
        $normalized = ltrim($version, 'v');
        if (strpos($normalized, $basePrefix) === 0) {
            // Exclude dev/alpha/beta/RC versions
            if (!preg_match('/(dev|alpha|beta|rc)/i', $version)) {
                $matchingVersions[] = $normalized;
            }
        }
    }

    if (empty($matchingVersions)) {
        return null;
    }

    // Sort versions and take the latest one
    usort($matchingVersions, 'version_compare');
    return end($matchingVersions);
}

// Function to get GitHub repository URL from Packagist
function getGitHubRepoFromPackagist($packageName) {
    $url = "https://packagist.org/packages/{$packageName}.json";
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Composer Update Helper',
        ]
    ]);

    $json = @file_get_contents($url, false, $context);
    if (!$json) {
        return null;
    }

    $data = json_decode($json, true);
    if (!$data || !isset($data['package']['repository'])) {
        return null;
    }

    $repoUrl = $data['package']['repository'];
    // Extract GitHub repo from URL (e.g., https://github.com/user/repo.git -> user/repo)
    if (preg_match('#github\.com[:/]([^/]+/[^/]+?)(?:\.git)?/?$#', $repoUrl, $matches)) {
        return $matches[1];
    }

    return null;
}

// Function to get release information from GitHub
function getReleaseInfo($githubRepo, $version) {
    if (!$githubRepo) {
        return null;
    }

    // Normalize version (remove 'v' prefix if present)
    $normalizedVersion = ltrim($version, 'v');

    // Try to get release by tag
    $url = "https://api.github.com/repos/{$githubRepo}/releases/tags/v{$normalizedVersion}";
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Composer Update Helper',
            'header' => 'Accept: application/vnd.github.v3+json',
        ]
    ]);

    $json = @file_get_contents($url, false, $context);
    if ($json) {
        $release = json_decode($json, true);
        if ($release && isset($release['html_url'])) {
            return [
                'url' => $release['html_url'],
                'name' => $release['name'] ?? $release['tag_name'] ?? $version,
                'body' => $release['body'] ?? '',
                'published_at' => $release['published_at'] ?? null,
            ];
        }
    }

    // If not found, try without 'v' prefix
    $url = "https://api.github.com/repos/{$githubRepo}/releases/tags/{$normalizedVersion}";
    $json = @file_get_contents($url, false, $context);
    if ($json) {
        $release = json_decode($json, true);
        if ($release && isset($release['html_url'])) {
            return [
                'url' => $release['html_url'],
                'name' => $release['name'] ?? $release['tag_name'] ?? $version,
                'body' => $release['body'] ?? '',
                'published_at' => $release['published_at'] ?? null,
            ];
        }
    }

    // Try latest release if exact version not found
    $url = "https://api.github.com/repos/{$githubRepo}/releases/latest";
    $json = @file_get_contents($url, false, $context);
    if ($json) {
        $release = json_decode($json, true);
        if ($release && isset($release['html_url'])) {
            $latestVersion = ltrim($release['tag_name'] ?? '', 'v');
            if ($latestVersion === $normalizedVersion) {
                return [
                    'url' => $release['html_url'],
                    'name' => $release['name'] ?? $release['tag_name'] ?? $version,
                    'body' => $release['body'] ?? '',
                    'published_at' => $release['published_at'] ?? null,
                ];
            }
        }
    }

    return null;
}

// ============================================================================
// PROCESS PACKAGES
// ============================================================================

$prod = [];
$dev  = [];
$ignoredProd = [];
$ignoredDev  = [];
$releaseInfo = []; // Store release information for packages

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
            $normalized = ltrim($latest, 'v');
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

    $normalized = ltrim($latest, 'v');
    $installedNormalized = $installed ? ltrim($installed, 'v') : null;

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
        } elseif ($debug) {
            error_log("DEBUG:   - Wildcard constraint, including for update");
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

    if (isset($devSet[$name])) {
        $dev[] = $name . ':' . $constraint;
        if ($debug) {
            error_log("DEBUG:   - Action: ADDED to dev dependencies: {$name}:{$constraint}");
        }
    } else {
        $prod[] = $name . ':' . $constraint;
        if ($debug) {
            error_log("DEBUG:   - Action: ADDED to prod dependencies: {$name}:{$constraint}");
        }
    }
}

// ============================================================================
// OUTPUT FORMATTING
// ============================================================================

// Check if there's anything to show
if (empty($prod) && empty($dev) && empty($ignoredProd) && empty($ignoredDev)) {
    if ($verbose || $debug) {
        error_log("ℹ️  No outdated direct dependencies found.");
    }
    echo E_OK . "  No outdated direct dependencies." . PHP_EOL;
    exit(0);
}

$output = [];

// Show detected frameworks
$detectedFrameworks = [];
foreach ($frameworkConstraints as $prefix => $version) {
    $detectedFrameworks[] = rtrim($prefix, '/') . ' ' . $version . '.*';
}
if (!empty($detectedFrameworks)) {
    $output[] = E_WRENCH . "  Detected framework constraints:";
    foreach ($detectedFrameworks as $fw) {
        $output[] = "  - " . $fw;
    }
    $output[] = "";
}

// Show ignored packages (prod)
if (!empty($ignoredProd)) {
    $output[] = E_SKIP . "   Ignored packages (prod):";
    foreach ($ignoredProd as $pkg) {
        $output[] = "  - " . $pkg;
    }
    $output[] = "";
}

// Show ignored packages (dev)
if (!empty($ignoredDev)) {
    $output[] = E_SKIP . "   Ignored packages (dev):";
    foreach ($ignoredDev as $pkg) {
        $output[] = "  - " . $pkg;
    }
    $output[] = "";
}

// Commands section (with special markers for extraction)
$commandsList = [];
if (!empty($prod)) {
    $commandsList[] = "composer require --with-all-dependencies " . implode(' ', $prod);
}
if (!empty($dev)) {
    $commandsList[] = "composer require --dev --with-all-dependencies " . implode(' ', $dev);
}

if (empty($commandsList)) {
    $output[] = E_OK . "  No packages to update (all outdated packages are ignored).";
} else {
    $output[] = E_WRENCH . "  Suggested commands:";
    foreach ($commandsList as $cmd) {
        $output[] = "  " . $cmd;
    }

    // Add special markers for command extraction (for --run flag)
    $output[] = "---COMMANDS_START---";
    foreach ($commandsList as $cmd) {
        $output[] = $cmd;
    }
    $output[] = "---COMMANDS_END---";
}

// Release information section
if ($showReleaseInfo && !empty($releaseInfo)) {
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
}

if ($debug) {
    error_log("DEBUG: Generated output:");
    error_log("DEBUG:   - Prod packages: " . count($prod) . " (" . implode(', ', $prod) . ")");
    error_log("DEBUG:   - Dev packages: " . count($dev) . " (" . implode(', ', $dev) . ")");
    error_log("DEBUG:   - Ignored prod: " . count($ignoredProd) . " (" . implode(', ', $ignoredProd) . ")");
    error_log("DEBUG:   - Ignored dev: " . count($ignoredDev) . " (" . implode(', ', $ignoredDev) . ")");
    error_log("DEBUG:   - Commands: " . count($commandsList));
}

echo implode(PHP_EOL, $output);

