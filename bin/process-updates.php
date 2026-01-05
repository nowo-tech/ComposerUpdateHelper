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

// Function to read a configuration value from YAML file
function readConfigValue(string $yamlPath, string $key, $default = null)
{
    if (!file_exists($yamlPath)) {
        return $default;
    }

    $content = file_get_contents($yamlPath);
    if ($content === false) {
        return $default;
    }

    $lines = explode("\n", $content);

    foreach ($lines as $line) {
        $trimmedLine = trim($line);

        // Skip empty lines and pure comment lines
        if (empty($trimmedLine) || strpos($trimmedLine, '#') === 0) {
            continue;
        }

        // Check for key: value pattern
        if (preg_match('/^' . preg_quote($key, '/') . ':\s*(.+)$/', $trimmedLine, $matches)) {
            $value = trim($matches[1]);
            // Handle boolean values
            if (strtolower($value) === 'true') {
                return true;
            }
            if (strtolower($value) === 'false') {
                return false;
            }
            // Handle numeric values
            if (is_numeric($value)) {
                return $value + 0; // Convert to int or float
            }
            // Return as string
            return $value;
        }
    }

    return $default;
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
$checkDependencies = true; // Default: enabled

if ($configFile) {
    // YAML file provided
    if (strpos($configFile, '.yaml') !== false || strpos($configFile, '.yml') !== false) {
        $ignoredPackages = readPackagesFromYaml($configFile, 'ignore');
        $includedPackages = readPackagesFromYaml($configFile, 'include');
        $checkDependencies = readConfigValue($configFile, 'check-dependencies', true);
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
        $checkDependencies = readConfigValue($yamlFile, 'check-dependencies', true);
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
    error_log("DEBUG: checkDependencies = " . ($checkDependencies ? 'true' : 'false'));
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

// Function to get packages that depend on a given package
function getDependentPackages($packageName) {
    $composerBin = getenv('COMPOSER_BIN') ?: 'composer';
    $phpBin = getenv('PHP_BIN') ?: 'php';

    // Run composer why to get dependent packages
    $cmd = escapeshellarg($phpBin) . ' -d date.timezone=UTC ' . escapeshellarg($composerBin) .
           ' why ' . escapeshellarg($packageName) . ' 2>/dev/null';

    $output = shell_exec($cmd);
    if (!$output) {
        return [];
    }

    $dependents = [];
    $lines = explode("\n", trim($output));

    foreach ($lines as $line) {
        // Parse lines like "package/name  version  requires  target-package (constraint)"
        if (preg_match('/^([a-z0-9\-_]+\/[a-z0-9\-_]+)\s+/i', $line, $matches)) {
            $dependentPackage = $matches[1];
            // Extract version constraint if present
            if (preg_match('/requires\s+' . preg_quote($packageName, '/') . '\s+\(([^)]+)\)/', $line, $constraintMatches)) {
                $dependents[$dependentPackage] = $constraintMatches[1];
            } else {
                $dependents[$dependentPackage] = null;
            }
        }
    }

    return $dependents;
}

// Function to get version constraints from composer.lock for a package
function getPackageConstraintsFromLock($packageName) {
    if (!file_exists('composer.lock')) {
        return [];
    }

    $lock = json_decode(file_get_contents('composer.lock'), true);
    if (!$lock || !isset($lock['packages']) && !isset($lock['packages-dev'])) {
        return [];
    }

    $allPackages = array_merge(
        $lock['packages'] ?? [],
        $lock['packages-dev'] ?? []
    );

    $constraints = [];
    foreach ($allPackages as $pkg) {
        if (!isset($pkg['name']) || !isset($pkg['require'])) {
            continue;
        }

        // Check if this package requires our target package
        if (isset($pkg['require'][$packageName])) {
            $constraints[$pkg['name']] = $pkg['require'][$packageName];
        }
    }

    return $constraints;
}

// Function to check if a version satisfies a constraint
function versionSatisfiesConstraint($version, $constraint) {
    if (empty($constraint)) {
        return true;
    }

    // Normalize version
    $normalizedVersion = ltrim($version, 'v');
    $constraint = trim($constraint);

    // Handle constraints that start with 'v' followed by version (e.g., "v8.2.0" means exactly "8.2.0")
    if (preg_match('/^v(\d+\.\d+\.\d+)$/', $constraint, $matches)) {
        return version_compare($normalizedVersion, $matches[1], '==');
    }

    // Handle wildcard constraints (e.g., "8.1.*")
    if (preg_match('/^(\d+\.\d+)\.\*$/', $constraint, $matches)) {
        $baseVersion = $matches[1];
        // Check if version starts with base version (e.g., "8.1.0", "8.1.5" match "8.1.*")
        return strpos($normalizedVersion, $baseVersion . '.') === 0;
    }

    // Handle range constraints with comma (AND) or pipe (OR)
    // Note: Composer uses both single | and double || for OR
    // IMPORTANT: This must be checked BEFORE caret/tilde constraints because
    // constraints like "^2.5|^3" need to be split first
    if (strpos($constraint, '||') !== false || strpos($constraint, '|') !== false) {
        // OR operator: any range must be satisfied
        // Split by || first, then by | to handle both formats
        $ranges = preg_split('/\s*\|\|\s*|\s*\|\s*/', $constraint);
        foreach ($ranges as $range) {
            $range = trim($range);
            if (empty($range)) {
                continue;
            }
            // Recursively check each range
            if (versionSatisfiesConstraint($version, $range)) {
                return true;
            }
        }
        return false;
    }

    // Handle caret constraints (e.g., "^8.1.0" means >=8.1.0 <9.0.0, "^2.0" means >=2.0.0 <3.0.0, "^3" means >=3.0.0 <4.0.0)
    // Also handle "^v7.1.0" which should be treated as "^7.1.0" (ignore the 'v' prefix)
    // Also handle "^3.0" which means >=3.0.0 <4.0.0
    if (preg_match('/^\^v?(\d+)(?:\.(\d+))?(?:\.(\d+))?/', $constraint, $matches)) {
        $major = (int)$matches[1];
        // Check if minor and patch are captured (not just empty strings)
        $minor = (isset($matches[2]) && $matches[2] !== '') ? (int)$matches[2] : 0;
        $patch = (isset($matches[3]) && $matches[3] !== '') ? (int)$matches[3] : 0;

        $minVersion = $major . '.' . $minor . '.' . $patch;
        $nextMajor = $major + 1;
        $maxVersion = $nextMajor . '.0.0';

        $result = version_compare($normalizedVersion, $minVersion, '>=') &&
                  version_compare($normalizedVersion, $maxVersion, '<');

        return $result;
    }

    // Handle tilde constraints (e.g., "~8.1.0" means >=8.1.0 <8.2.0, "~1.0" means >=1.0.0 <2.0.0)
    // Also handle "~v7.1.0" which should be treated as "~7.1.0" (ignore the 'v' prefix)
    if (preg_match('/^~v?(\d+)(?:\.(\d+))?(?:\.(\d+))?/', $constraint, $matches)) {
        $major = (int)$matches[1];
        $minor = isset($matches[2]) && $matches[2] !== '' ? (int)$matches[2] : 0;
        $patch = isset($matches[3]) && $matches[3] !== '' ? (int)$matches[3] : 0;

        $minVersion = $major . '.' . $minor . '.' . $patch;

        // If only major version specified (e.g., "~1"), next version is major+1.0.0
        // If major.minor specified (e.g., "~1.0"), next version is major.minor+1.0
        // If major.minor.patch specified (e.g., "~1.0.0"), next version is major.minor+1.0
        if (!isset($matches[2]) || $matches[2] === '') {
            // Only major: ~1 means >=1.0.0 <2.0.0
            $nextMajor = $major + 1;
            $maxVersion = $nextMajor . '.0.0';
        } else {
            // Major.minor or major.minor.patch: ~1.0 means >=1.0.0 <2.0.0, ~1.0.0 means >=1.0.0 <1.1.0
            if (!isset($matches[3]) || $matches[3] === '') {
                // Major.minor: ~1.0 means >=1.0.0 <2.0.0
                $nextMajor = $major + 1;
                $maxVersion = $nextMajor . '.0.0';
            } else {
                // Major.minor.patch: ~1.0.0 means >=1.0.0 <1.1.0
                $nextMinor = $minor + 1;
                $maxVersion = $major . '.' . $nextMinor . '.0';
            }
        }

        return version_compare($normalizedVersion, $minVersion, '>=') &&
               version_compare($normalizedVersion, $maxVersion, '<');
    }

    if (strpos($constraint, ',') !== false) {
        // AND operator: all ranges must be satisfied
        $ranges = explode(',', $constraint);
        foreach ($ranges as $range) {
            $range = trim($range);
            if (!versionSatisfiesConstraint($version, $range)) {
                return false;
            }
        }
        return true;
    }

    // Handle simple comparison operators (>=, <=, >, <, ==, !=)
    if (preg_match('/^(>=|<=|>|<|==|!=)\s*(.+)$/', $constraint, $matches)) {
        $operator = $matches[1];
        $targetVersion = ltrim($matches[2], 'v');

        // Handle != operator
        if ($operator === '!=') {
            return version_compare($normalizedVersion, $targetVersion, '!=');
        }

        return version_compare($normalizedVersion, $targetVersion, $operator);
    }

    // Handle exact version match (e.g., "8.1.0")
    $constraintNormalized = ltrim($constraint, 'v');
    if (preg_match('/^\d+\.\d+\.\d+/', $constraintNormalized)) {
        return version_compare($normalizedVersion, $constraintNormalized, '==');
    }

    // Default: try to use Composer's constraint parser if available
    // For now, fallback to simple comparison
    // This handles cases like "8.1" which might mean "8.1.*"
    if (preg_match('/^(\d+\.\d+)$/', $constraint, $matches)) {
        $baseVersion = $matches[1];
        return strpos($normalizedVersion, $baseVersion . '.') === 0;
    }

    return false;
}

// Function to get package requirements from Packagist
function getPackageRequirements($packageName, $version) {
    $url = "https://packagist.org/packages/{$packageName}.json";
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Composer Update Helper',
        ]
    ]);

    $json = @file_get_contents($url, false, $context);
    if (!$json) {
        // Fallback: try composer show
        return getPackageRequirementsFromComposer($packageName, $version);
    }

    $data = json_decode($json, true);
    if (!$data || !isset($data['package']['versions'])) {
        // Fallback: try composer show
        return getPackageRequirementsFromComposer($packageName, $version);
    }

    // Normalize version (remove 'v' prefix)
    $normalizedVersion = ltrim($version, 'v');

    // Find the version in the package data
    foreach ($data['package']['versions'] as $versionKey => $versionData) {
        $versionKeyNormalized = ltrim($versionKey, 'v');
        if ($versionKeyNormalized === $normalizedVersion && isset($versionData['require'])) {
            return $versionData['require'];
        }
    }

    // Version not found in Packagist data, try composer show as fallback
    return getPackageRequirementsFromComposer($packageName, $version);
}

// Function to get package requirements from composer show (fallback)
function getPackageRequirementsFromComposer($packageName, $version) {
    $composerBin = getenv('COMPOSER_BIN') ?: 'composer';
    $phpBin = getenv('PHP_BIN') ?: 'php';

    // Try to get package info for the specific version
    $normalizedVersion = ltrim($version, 'v');
    $cmd = escapeshellarg($phpBin) . ' -d date.timezone=UTC ' . escapeshellarg($composerBin) .
           ' show ' . escapeshellarg($packageName . ':' . $normalizedVersion) . ' --format=json 2>/dev/null';

    $output = shell_exec($cmd);
    if (!$output) {
        return [];
    }

    $data = json_decode($output, true);
    if (!$data || !isset($data['requires'])) {
        return [];
    }

    return $data['requires'];
}

// Function to get installed package version from composer.json or composer.lock
function getInstalledPackageVersion($packageName) {
    // First try composer.lock (more accurate)
    if (file_exists('composer.lock')) {
        $lock = json_decode(file_get_contents('composer.lock'), true);
        if ($lock) {
            $allPackages = array_merge(
                $lock['packages'] ?? [],
                $lock['packages-dev'] ?? []
            );

            foreach ($allPackages as $pkg) {
                if (isset($pkg['name']) && $pkg['name'] === $packageName) {
                    $version = $pkg['version'] ?? '';
                    // Remove 'v' prefix if present
                    $version = ltrim($version, 'v');
                    // composer.lock may have versions like "3.6.0.0" (with extra patch), normalize to x.y.z
                    if (preg_match('/^(\d+\.\d+\.\d+)/', $version, $matches)) {
                        return $matches[1];
                    }
                    return $version;
                }
            }
        }
    }

    // Fallback to composer.json (less accurate, but better than nothing)
    // Note: composer.json has constraints, not exact versions
    // We'll try to extract a version from the constraint if possible
    if (file_exists('composer.json')) {
        $composer = json_decode(file_get_contents('composer.json'), true);
        $require = $composer['require'] ?? [];
        $requireDev = $composer['require-dev'] ?? [];
        $allDeps = array_merge($require, $requireDev);

        if (isset($allDeps[$packageName])) {
            $constraint = $allDeps[$packageName];
            // Try to extract version from constraint (e.g., "8.1.0" from "8.1.0" or "^8.1.0")
            if (preg_match('/(\d+\.\d+\.\d+)/', $constraint, $matches)) {
                return $matches[1];
            }
            // If no exact version found, return null to skip this check
            return null;
        }
    }

    return null;
}

// Function to find the highest compatible version considering dependent packages
function findCompatibleVersion($packageName, $proposedVersion, $debug = false, $checkDependencies = true) {
    // If dependency checking is disabled, return proposed version without verification
    if (!$checkDependencies) {
        if ($debug) {
            error_log("DEBUG: Dependency checking is disabled, using proposed version: {$proposedVersion}");
        }
        return $proposedVersion;
    }

    // Get dependent packages and their constraints
    $dependentConstraints = getPackageConstraintsFromLock($packageName);

    // Get requirements of the proposed package version
    $packageRequirements = getPackageRequirements($packageName, $proposedVersion);

    if ($debug && !empty($packageRequirements)) {
        error_log("DEBUG: Package {$packageName} {$proposedVersion} requires:");
        foreach ($packageRequirements as $req => $constraint) {
            error_log("DEBUG:   - {$req}: {$constraint}");
        }
    }

    // Check if the proposed package's requirements are compatible with installed versions
    foreach ($packageRequirements as $requiredPackage => $requiredConstraint) {
        // Skip php and php-* requirements
        if ($requiredPackage === 'php' || strpos($requiredPackage, 'php-') === 0) {
            continue;
        }

        // Skip ext-* requirements
        if (strpos($requiredPackage, 'ext-') === 0) {
            continue;
        }

        // Handle "self.version" constraint (package requires same version as itself)
        if ($requiredConstraint === 'self.version' || $requiredConstraint === '@self') {
            // This means the required package must be the same version as the proposing package
            // For example, scheb/2fa-google-authenticator 8.2.0 requires scheb/2fa-bundle: self.version
            // means it requires scheb/2fa-bundle 8.2.0
            $normalizedProposed = ltrim($proposedVersion, 'v');
            $requiredVersion = $normalizedProposed;

            // Get installed version of the required package
            $installedVersion = getInstalledPackageVersion($requiredPackage);
            if ($installedVersion === null) {
                // Package not installed, skip check (it will be installed if needed)
                continue;
            }

            $normalizedInstalled = ltrim($installedVersion, 'v');
            if ($normalizedInstalled !== $requiredVersion) {
                if ($debug) {
                    error_log("DEBUG: Proposed package {$packageName} {$proposedVersion} requires {$requiredPackage}: {$requiredConstraint} (which means {$requiredVersion}), but installed version {$normalizedInstalled} does NOT match");
                }
                // Conflict detected: self.version constraint requires exact version match
                return null;
            }
            // Version matches, continue to next requirement
            continue;
        }

        // Get installed version of the required package
        $installedVersion = getInstalledPackageVersion($requiredPackage);

        if ($installedVersion === null) {
            // Package not installed, skip check (it will be installed if needed)
            continue;
        }

        // Check if installed version satisfies the required constraint
        $satisfies = versionSatisfiesConstraint($installedVersion, $requiredConstraint);
        if ($debug) {
            error_log("DEBUG: Checking if installed version {$installedVersion} satisfies constraint {$requiredConstraint} for {$requiredPackage}: " . ($satisfies ? 'YES' : 'NO'));
        }

        if (!$satisfies) {
            if ($debug) {
                error_log("DEBUG: Proposed package {$packageName} {$proposedVersion} requires {$requiredPackage}: {$requiredConstraint}, but installed version {$installedVersion} does NOT satisfy it");
            }
            // Conflict detected: proposed package requires a version incompatible with installed version
            return null;
        }
    }

    if (empty($dependentConstraints)) {
        // No dependents, and requirements are compatible, safe to use proposed version
        if ($debug) {
            error_log("DEBUG: No dependent packages found for {$packageName}, and requirements are compatible, using proposed version: {$proposedVersion}");
        }
        return $proposedVersion;
    }

    if ($debug) {
        error_log("DEBUG: Found " . count($dependentConstraints) . " dependent packages for {$packageName}:");
        foreach ($dependentConstraints as $dep => $constraint) {
            error_log("DEBUG:   - {$dep} requires {$packageName}: {$constraint}");
        }
    }

    // Check if proposed version satisfies all constraints
    $allSatisfied = true;
    foreach ($dependentConstraints as $depPackage => $constraint) {
        if (!versionSatisfiesConstraint($proposedVersion, $constraint)) {
            $allSatisfied = false;
            if ($debug) {
                error_log("DEBUG: Proposed version {$proposedVersion} does NOT satisfy constraint '{$constraint}' from {$depPackage}");
            }
            break;
        }
    }

    if ($allSatisfied) {
        // All dependent constraints are satisfied, and package requirements are compatible
        if ($debug) {
            error_log("DEBUG: Proposed version {$proposedVersion} satisfies all dependent constraints and requirements are compatible");
        }
        return $proposedVersion;
    }

    // Need to find a compatible version
    // Get all available versions
    $composerBin = getenv('COMPOSER_BIN') ?: 'composer';
    $phpBin = getenv('PHP_BIN') ?: 'php';

    $cmd = escapeshellarg($phpBin) . ' -d date.timezone=UTC ' . escapeshellarg($composerBin) .
           ' show ' . escapeshellarg($packageName) . ' --all --format=json 2>/dev/null';

    $output = shell_exec($cmd);
    if (!$output) {
        if ($debug) {
            error_log("DEBUG: Could not get available versions for {$packageName}, skipping compatibility check");
        }
        return null; // Can't verify, skip this update
    }

    $data = json_decode($output, true);
    if (!$data || !isset($data['versions'])) {
        if ($debug) {
            error_log("DEBUG: No versions found for {$packageName}, skipping compatibility check");
        }
        return null;
    }

    // Filter stable versions and sort them
    $stableVersions = [];
    foreach ($data['versions'] as $version) {
        $normalized = ltrim($version, 'v');
        // Exclude dev/alpha/beta/RC versions
        if (!preg_match('/(dev|alpha|beta|rc)/i', $version)) {
            $stableVersions[] = $normalized;
        }
    }

    if (empty($stableVersions)) {
        if ($debug) {
            error_log("DEBUG: No stable versions found for {$packageName}");
        }
        return null;
    }

    // Sort versions descending
    usort($stableVersions, function($a, $b) {
        return version_compare($b, $a); // Descending order
    });

    // Find the highest version that satisfies all constraints and requirements
    foreach ($stableVersions as $version) {
        $satisfiesAll = true;

        // Check dependent constraints
        foreach ($dependentConstraints as $depPackage => $constraint) {
            if (!versionSatisfiesConstraint($version, $constraint)) {
                $satisfiesAll = false;
                break;
            }
        }

        if (!$satisfiesAll) {
            continue;
        }

        // Check package requirements for this version
        $versionRequirements = getPackageRequirements($packageName, $version);
        foreach ($versionRequirements as $requiredPackage => $requiredConstraint) {
            // Skip php and ext-* requirements
            if ($requiredPackage === 'php' || strpos($requiredPackage, 'php-') === 0 || strpos($requiredPackage, 'ext-') === 0) {
                continue;
            }

            $installedVersion = getInstalledPackageVersion($requiredPackage);
            if ($installedVersion === null) {
                continue; // Package not installed, will be installed if needed
            }

            if (!versionSatisfiesConstraint($installedVersion, $requiredConstraint)) {
                $satisfiesAll = false;
                break;
            }
        }

        if ($satisfiesAll) {
            if ($debug) {
                error_log("DEBUG: Found compatible version {$version} for {$packageName} (proposed was {$proposedVersion})");
            }
            return $version;
        }
    }

    // No compatible version found
    if ($debug) {
        error_log("DEBUG: No compatible version found for {$packageName} (proposed: {$proposedVersion})");
        foreach ($dependentConstraints as $depPackage => $constraint) {
            error_log("DEBUG:   - {$depPackage} requires: {$constraint}");
        }
    }
    return null; // No compatible version, skip this update
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

// Track all outdated packages (before dependency checking) for debug output
$allOutdatedProd = [];
$allOutdatedDev  = [];
$filteredByDependenciesProd = [];
$filteredByDependenciesDev  = [];

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
    }

    // Check dependency compatibility before suggesting update
    // Only check for specific versions (not wildcards) and if dependency checking is enabled
    if ($needsUpdate && $checkDependencies && strpos($constraint, '*') === false && strpos($constraint, '^') === false && strpos($constraint, '~') === false) {
        $compatibleVersion = findCompatibleVersion($name, $constraint, $debug, $checkDependencies);
        if ($compatibleVersion === null) {
            // No compatible version found, skip this update
            if ($debug) {
                error_log("DEBUG:   - Action: SKIPPED (no compatible version found due to dependency constraints)");
            }
            // Track filtered packages
            if (isset($devSet[$name])) {
                $filteredByDependenciesDev[] = $packageString;
            } else {
                $filteredByDependenciesProd[] = $packageString;
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
    $output[] = "";
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

// Show dependency checking comparison when enabled
if ($checkDependencies) {
    $output[] = E_WRENCH . "  Dependency checking analysis:";

    // Show all outdated packages (before checking)
    if (!empty($allOutdatedProd) || !empty($allOutdatedDev)) {
        $output[] = "  📋 All outdated packages (before dependency check):";
        if (!empty($allOutdatedProd)) {
            foreach ($allOutdatedProd as $pkg) {
                $output[] = "     - " . $pkg . " (prod)";
            }
        }
        if (!empty($allOutdatedDev)) {
            foreach ($allOutdatedDev as $pkg) {
                $output[] = "     - " . $pkg . " (dev)";
            }
        }
        $output[] = "";
    } else {
        $output[] = "  📋 All outdated packages (before dependency check): (none)";
        $output[] = "";
    }

    // Show filtered packages (conflicts detected)
    if (!empty($filteredByDependenciesProd) || !empty($filteredByDependenciesDev)) {
        $output[] = "  ⚠️  Filtered by dependency conflicts:";
        if (!empty($filteredByDependenciesProd)) {
            foreach ($filteredByDependenciesProd as $pkg) {
                $output[] = "     - " . $pkg . " (prod)";
            }
        }
        if (!empty($filteredByDependenciesDev)) {
            foreach ($filteredByDependenciesDev as $pkg) {
                $output[] = "     - " . $pkg . " (dev)";
            }
        }
        $output[] = "";
    } else {
        $output[] = "  ⚠️  Filtered by dependency conflicts: (none)";
        $output[] = "";
    }

    // Show packages that passed dependency check
    if (!empty($prod) || !empty($dev)) {
        $output[] = "  ✅ Packages that passed dependency check:";
        if (!empty($prod)) {
            foreach ($prod as $pkg) {
                $output[] = "     - " . $pkg . " (prod)";
            }
        }
        if (!empty($dev)) {
            foreach ($dev as $pkg) {
                $output[] = "     - " . $pkg . " (dev)";
            }
        }
        $output[] = "";
    } else {
        $output[] = "  ✅ Packages that passed dependency check: (none)";
        $output[] = "";
    }
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

