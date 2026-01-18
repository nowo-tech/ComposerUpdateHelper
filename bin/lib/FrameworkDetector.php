<?php

declare(strict_types=1);

/**
 * Framework Detector
 * Handles framework detection and version constraint limiting
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

class FrameworkDetector
{
    private static array $frameworkConfigs = [
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

    /**
     * Extract the base version from a constraint or version (e.g.: "7.4.*" -> "7.4", "^8.0" -> "8.0")
     */
    public static function extractBaseVersion($constraint): ?string
    {
        // Remove special characters and get the main numeric part
        $constraint = ltrim($constraint, '^~>=<vV');
        $parts = preg_split('/[^0-9]/', $constraint, 3);
        if (count($parts) >= 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
            return $parts[0] . '.' . $parts[1];
        }
        return null;
    }

    /**
     * Detect framework constraints from composer.json and installed packages
     */
    public static function detectFrameworkConstraints(array $composer, array $allDeps, bool $debug = false): array
    {
        $frameworkConstraints = [];

        // Detect Symfony constraint from extra.symfony.require
        if (isset($composer['extra']['symfony']['require'])) {
            $baseVersion = self::extractBaseVersion($composer['extra']['symfony']['require']);
            if ($baseVersion) {
                $frameworkConstraints['symfony/'] = $baseVersion;
                if ($debug) {
                    error_log("DEBUG: Detected Symfony constraint: {$baseVersion}.* (from extra.symfony.require)");
                }
            }
        }

        // Detect other frameworks from installed versions
        foreach (self::$frameworkConfigs as $name => $config) {
            if ($name === 'symfony') continue; // Already handled above

            $prefix = $config['prefix'];
            if (isset($frameworkConstraints[$prefix])) continue;

            // Try core package
            $corePackage = $config['corePackage'] ?? null;
            if ($corePackage && isset($allDeps[$corePackage])) {
                $baseVersion = self::extractBaseVersion($allDeps[$corePackage]);
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
                $baseVersion = self::extractBaseVersion($allDeps[$fallbackCore]);
                if ($baseVersion) {
                    $frameworkConstraints[$prefix] = $baseVersion;
                }
            }
        }

        return $frameworkConstraints;
    }

    /**
     * Get framework constraint for a package name
     */
    public static function getFrameworkConstraint(string $packageName, array $frameworkConstraints): ?string
    {
        foreach ($frameworkConstraints as $prefix => $baseVersion) {
            if (strpos($packageName, $prefix) === 0) {
                return $baseVersion;
            }
        }
        return null;
    }

    /**
     * Check if a version exceeds the framework constraint
     */
    public static function shouldLimitVersion(string $packageName, string $latestVersion, array $frameworkConstraints): bool
    {
        $constraintBase = self::getFrameworkConstraint($packageName, $frameworkConstraints);
        if (!$constraintBase) {
            return false;
        }

        // Normalize latest version
        $latest = ltrim($latestVersion, 'v');
        $latestBase = self::extractBaseVersion($latest);
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

    /**
     * Get the latest specific version that meets a constraint
     */
    public static function getLatestVersionInConstraint(string $packageName, string $baseVersion): ?string
    {
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
}
