<?php

declare(strict_types=1);

/**
 * Dependency Analyzer
 * Handles dependency analysis and version constraint satisfaction
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

class DependencyAnalyzer
{
    /**
     * Get packages that depend on a given package
     */
    public static function getDependentPackages(string $packageName): array
    {
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

    /**
     * Get version constraints from composer.lock for a package
     */
    public static function getPackageConstraintsFromLock(string $packageName): array
    {
        if (!file_exists('composer.lock')) {
            return [];
        }

        $lock = json_decode(file_get_contents('composer.lock'), true);
        if (!$lock || (!isset($lock['packages']) && !isset($lock['packages-dev']))) {
            return [];
        }

        $allPackages = array_merge(
            $lock['packages'] ?? [],
            $lock['packages-dev'] ?? []
        );

        $constraints = [];
        foreach ($allPackages as $pkg) {
            if (!isset($pkg['name'])) {
                continue;
            }

            // Check if this package requires our target package
            // Dependencies can be in 'require', 'require-dev', or both
            $requires = array_merge(
                $pkg['require'] ?? [],
                $pkg['require-dev'] ?? []
            );

            if (isset($requires[$packageName])) {
                $constraints[$pkg['name']] = $requires[$packageName];
            }
        }

        return $constraints;
    }

    /**
     * Check if a version satisfies a constraint
     */
    public static function versionSatisfiesConstraint(string $version, string $constraint): bool
    {
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
                if (self::versionSatisfiesConstraint($version, $range)) {
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

        // Handle range constraints with comma (AND)
        if (strpos($constraint, ',') !== false) {
            $ranges = explode(',', $constraint);
            foreach ($ranges as $range) {
                $range = trim($range);
                if (!self::versionSatisfiesConstraint($version, $range)) {
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

    /**
     * Get installed package version from composer.json or composer.lock
     */
    public static function getInstalledPackageVersion(string $packageName): ?string
    {
        // First, try composer.lock (more accurate)
        if (file_exists('composer.lock')) {
            $lock = json_decode(file_get_contents('composer.lock'), true);
            if ($lock) {
                $allPackages = array_merge(
                    $lock['packages'] ?? [],
                    $lock['packages-dev'] ?? []
                );

                foreach ($allPackages as $pkg) {
                    if (isset($pkg['name']) && $pkg['name'] === $packageName) {
                        return $pkg['version'] ?? null;
                    }
                }
            }
        }

        // Fallback: try composer.json
        if (file_exists('composer.json')) {
            $composer = json_decode(file_get_contents('composer.json'), true);
            if ($composer) {
                $requires = array_merge(
                    $composer['require'] ?? [],
                    $composer['require-dev'] ?? []
                );

                if (isset($requires[$packageName])) {
                    return $requires[$packageName];
                }
            }
        }

        return null;
    }
}
