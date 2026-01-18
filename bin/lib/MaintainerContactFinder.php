<?php

declare(strict_types=1);

/**
 * Maintainer Contact Finder
 * Finds maintainer contact information for packages when manual intervention is needed
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

class MaintainerContactFinder
{
    /**
     * Get maintainer contact information for a package
     *
     * @param string $packageName Package name
     * @param bool $debug Debug mode
     * @return array Maintainer information with contact details
     */
    public static function getMaintainerInfo(string $packageName, bool $debug = false): array
    {
        $maintainerInfo = [
            'package' => $packageName,
            'maintainers' => [],
            'repository_url' => null,
            'repository_type' => null,
            'last_update' => null,
            'is_stale' => false,
        ];

        // Get package info from Packagist
        $packagistData = self::getPackagistPackageInfo($packageName, $debug);
        if ($packagistData) {
            // Extract maintainers
            if (isset($packagistData['package']['maintainers'])) {
                foreach ($packagistData['package']['maintainers'] as $maintainer) {
                    $maintainerInfo['maintainers'][] = [
                        'name' => $maintainer['name'] ?? '',
                        'email' => $maintainer['email'] ?? null,
                        'homepage' => $maintainer['homepage'] ?? null,
                    ];
                }
            }

            // Extract repository URL
            if (isset($packagistData['package']['repository'])) {
                $repoUrl = $packagistData['package']['repository'];
                $maintainerInfo['repository_url'] = $repoUrl;
                
                // Detect repository type
                if (strpos($repoUrl, 'github.com') !== false) {
                    $maintainerInfo['repository_type'] = 'github';
                } elseif (strpos($repoUrl, 'gitlab.com') !== false) {
                    $maintainerInfo['repository_type'] = 'gitlab';
                } elseif (strpos($repoUrl, 'bitbucket.org') !== false) {
                    $maintainerInfo['repository_type'] = 'bitbucket';
                }
            }

            // Extract last update (from time field)
            if (isset($packagistData['package']['time'])) {
                $maintainerInfo['last_update'] = $packagistData['package']['time'];
                
                // Check if package is stale (>2 years old)
                $lastUpdateTime = strtotime($maintainerInfo['last_update']);
                $twoYearsAgo = time() - (2 * 365 * 24 * 60 * 60);
                $maintainerInfo['is_stale'] = $lastUpdateTime < $twoYearsAgo;
            }
        }

        return $maintainerInfo;
    }

    /**
     * Check if maintainer contact should be suggested
     *
     * @param string $packageName Package name
     * @param string $conflictingPackage Conflicting package name
     * @param string $constraint1 First constraint
     * @param string $constraint2 Second constraint
     * @param bool $debug Debug mode
     * @return bool True if maintainer contact should be suggested
     */
    public static function shouldSuggestContact(
        string $packageName,
        string $conflictingPackage,
        string $constraint1,
        string $constraint2,
        bool $debug = false
    ): bool {
        // Check if constraints are completely incompatible (no overlap)
        if (!self::constraintsOverlap($constraint1, $constraint2)) {
            // Check if package is stale
            $maintainerInfo = self::getMaintainerInfo($packageName, $debug);
            if ($maintainerInfo['is_stale']) {
                if ($debug) {
                    error_log("DEBUG: Suggesting maintainer contact - package {$packageName} is stale (last update: {$maintainerInfo['last_update']})");
                }
                return true;
            }
            
            // Check if constraints are on different major versions
            if (self::areDifferentMajorVersions($constraint1, $constraint2)) {
                if ($debug) {
                    error_log("DEBUG: Suggesting maintainer contact - major version conflict between {$constraint1} and {$constraint2}");
                }
                return true;
            }
        }

        return false;
    }

    /**
     * Get Packagist package information
     *
     * @param string $packageName Package name
     * @param bool $debug Debug mode
     * @return array|null Package data from Packagist or null
     */
    private static function getPackagistPackageInfo(string $packageName, bool $debug = false): ?array
    {
        $url = "https://packagist.org/packages/{$packageName}.json";
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'Composer Update Helper',
            ]
        ]);

        $json = @file_get_contents($url, false, $context);
        if (!$json) {
            if ($debug) {
                error_log("DEBUG: Could not fetch Packagist data for {$packageName}");
            }
            return null;
        }

        $data = json_decode($json, true);
        if (!$data || !isset($data['package'])) {
            if ($debug) {
                error_log("DEBUG: Invalid Packagist data for {$packageName}");
            }
            return null;
        }

        return $data;
    }

    /**
     * Check if two constraints overlap
     *
     * @param string $constraint1 First constraint
     * @param string $constraint2 Second constraint
     * @return bool True if constraints overlap
     */
    private static function constraintsOverlap(string $constraint1, string $constraint2): bool
    {
        // Simple check: if both are specific versions, check if they're equal
        // For more complex constraints, we'd need a proper constraint parser
        // This is a simplified version that works for basic cases
        
        // Remove version operators for comparison
        $clean1 = preg_replace('/[<>=\^~!]/', '', $constraint1);
        $clean2 = preg_replace('/[<>=\^~!]/', '', $constraint2);
        
        // If they're the same base version, they overlap
        if ($clean1 === $clean2) {
            return true;
        }
        
        // For caret/tilde constraints, check if ranges overlap
        // This is simplified - in practice, we'd use Composer's constraint parser
        if ((strpos($constraint1, '^') === 0 || strpos($constraint1, '~') === 0) &&
            (strpos($constraint2, '^') === 0 || strpos($constraint2, '~') === 0)) {
            // Extract base version
            $base1 = preg_replace('/[^0-9.]/', '', $constraint1);
            $base2 = preg_replace('/[^0-9.]/', '', $constraint2);
            
            // If base versions are close (same major for ^, same minor for ~), they might overlap
            $parts1 = explode('.', $base1);
            $parts2 = explode('.', $base2);
            
            if (count($parts1) > 0 && count($parts2) > 0) {
                // For ^, same major version means overlap
                if (strpos($constraint1, '^') === 0 && strpos($constraint2, '^') === 0) {
                    return $parts1[0] === $parts2[0];
                }
                // For ~, same major.minor means overlap
                if (strpos($constraint1, '~') === 0 && strpos($constraint2, '~') === 0) {
                    return count($parts1) > 1 && count($parts2) > 1 &&
                           $parts1[0] === $parts2[0] && $parts1[1] === $parts2[1];
                }
            }
        }
        
        // Default: assume they might overlap (conservative approach)
        return true;
    }

    /**
     * Check if two constraints are on different major versions
     *
     * @param string $constraint1 First constraint
     * @param string $constraint2 Second constraint
     * @return bool True if constraints are on different major versions
     */
    private static function areDifferentMajorVersions(string $constraint1, string $constraint2): bool
    {
        // Extract major version numbers
        $major1 = self::extractMajorVersion($constraint1);
        $major2 = self::extractMajorVersion($constraint2);
        
        return $major1 !== null && $major2 !== null && $major1 !== $major2;
    }

    /**
     * Extract major version from constraint
     *
     * @param string $constraint Version constraint
     * @return int|null Major version number or null
     */
    private static function extractMajorVersion(string $constraint): ?int
    {
        // Remove operators
        $clean = preg_replace('/[<>=\^~!*]/', '', $constraint);
        
        // Extract first number
        if (preg_match('/^(\d+)/', $clean, $matches)) {
            return (int)$matches[1];
        }
        
        return null;
    }

    /**
     * Generate repository issue URL
     *
     * @param string $repositoryUrl Repository URL
     * @param string $repositoryType Repository type (github, gitlab, bitbucket)
     * @return string|null Issue URL or null
     */
    public static function generateIssueUrl(string $repositoryUrl, string $repositoryType): ?string
    {
        if ($repositoryType === 'github') {
            // Extract user/repo from GitHub URL
            if (preg_match('#github\.com[:/]([^/]+/[^/]+?)(?:\.git)?/?$#', $repositoryUrl, $matches)) {
                return "https://github.com/{$matches[1]}/issues/new";
            }
        } elseif ($repositoryType === 'gitlab') {
            // Extract user/repo from GitLab URL
            if (preg_match('#gitlab\.com[:/]([^/]+/[^/]+?)(?:\.git)?/?$#', $repositoryUrl, $matches)) {
                return "https://gitlab.com/{$matches[1]}/-/issues/new";
            }
        } elseif ($repositoryType === 'bitbucket') {
            // Extract user/repo from Bitbucket URL
            if (preg_match('#bitbucket\.org[:/]([^/]+/[^/]+?)(?:\.git)?/?$#', $repositoryUrl, $matches)) {
                return "https://bitbucket.org/{$matches[1]}/issues/new";
            }
        }
        
        return null;
    }
}
