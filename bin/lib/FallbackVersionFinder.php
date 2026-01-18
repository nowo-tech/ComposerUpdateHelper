<?php

declare(strict_types=1);

/**
 * Fallback Version Finder
 * Finds compatible fallback versions when primary updates conflict
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

class FallbackVersionFinder
{
    /**
     * Find a fallback version of a package that satisfies conflicting dependencies
     *
     * @param string $packageName Package name
     * @param string $targetVersion Target version (newer version that conflicts)
     * @param array $conflictingDependents Array of ['package' => 'constraint'] pairs
     * @param bool $debug Enable debug logging
     * @return string|null Compatible fallback version or null if none found
     */
    public static function findFallbackVersion(
        string $packageName,
        string $targetVersion,
        array $conflictingDependents,
        bool $debug = false
    ): ?string {
        if (empty($conflictingDependents)) {
            return null; // No conflicts, no need for fallback
        }
        
        // Get all available versions
        $composerBin = getenv('COMPOSER_BIN') ?: 'composer';
        $phpBin = getenv('PHP_BIN') ?: 'php';
        
        $cmd = escapeshellarg($phpBin) . ' -d date.timezone=UTC ' . escapeshellarg($composerBin) .
               ' show ' . escapeshellarg($packageName) . ' --all --format=json 2>/dev/null';
        
        $output = shell_exec($cmd);
        if (!$output) {
            if ($debug) {
                error_log("DEBUG: Could not get versions for fallback search of {$packageName}");
            }
            return null;
        }
        
        $data = json_decode($output, true);
        if (!$data || !isset($data['versions'])) {
            return null;
        }
        
        // Filter stable versions and sort descending
        $stableVersions = [];
        foreach ($data['versions'] as $version) {
            $normalized = ltrim($version, 'v');
            if (!preg_match('/(dev|alpha|beta|rc)/i', $version)) {
                $stableVersions[] = $normalized;
            }
        }
        
        if (empty($stableVersions)) {
            return null;
        }
        
        usort($stableVersions, function($a, $b) {
            return version_compare($b, $a); // Descending
        });
        
        // Find highest version that satisfies all conflicting constraints
        // (but is lower than target version)
        foreach ($stableVersions as $version) {
            // Skip if version is >= target (we want a fallback, not the same or higher)
            if (version_compare($version, $targetVersion, '>=')) {
                continue;
            }
            
            // Check if this version satisfies all conflicting constraints
            $satisfiesAll = true;
            foreach ($conflictingDependents as $depPackage => $constraint) {
                if (!DependencyAnalyzer::versionSatisfiesConstraint($version, $constraint)) {
                    $satisfiesAll = false;
                    if ($debug) {
                        error_log("DEBUG: Fallback version {$version} does not satisfy constraint '{$constraint}' from {$depPackage}");
                    }
                    break;
                }
            }
            
            if ($satisfiesAll) {
                if ($debug) {
                    error_log("DEBUG: Found fallback version {$version} for {$packageName} that satisfies all conflicting constraints");
                }
                return $version;
            }
        }
        
        if ($debug) {
            error_log("DEBUG: No fallback version found for {$packageName} that satisfies all conflicting constraints");
        }
        
        return null;
    }
}
