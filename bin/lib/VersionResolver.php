<?php

declare(strict_types=1);

/**
 * Version Resolver
 * Finds compatible versions considering dependent packages and requirements
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

class VersionResolver
{
    /**
     * Find the highest compatible version considering dependent packages
     *
     * @param string $packageName Package name
     * @param string $proposedVersion Proposed version
     * @param bool $debug Enable debug logging
     * @param bool $checkDependencies Enable dependency checking
     * @param array|null $requiredTransitiveUpdates Output array for transitive updates
     * @param array|null $conflictingDependents Output array for conflicting dependents
     * @return string|null Compatible version or null if none found
     */
    public static function findCompatibleVersion(
        string $packageName,
        string $proposedVersion,
        bool $debug = false,
        bool $checkDependencies = true,
        ?array &$requiredTransitiveUpdates = null,
        ?array &$conflictingDependents = null
    ): ?string {
        // If dependency checking is disabled, return proposed version without verification
        if (!$checkDependencies) {
            if ($debug) {
                error_log("DEBUG: Dependency checking is disabled, using proposed version: {$proposedVersion}");
            }
            return $proposedVersion;
        }

        // Get dependent packages and their constraints
        $dependentConstraints = DependencyAnalyzer::getPackageConstraintsFromLock($packageName);

        // FIRST: Check if proposed version satisfies all constraints from dependent packages
        $proposedSatisfiesDependents = true;
        if (!empty($dependentConstraints)) {
            if ($debug) {
                error_log("DEBUG: Found " . count($dependentConstraints) . " dependent packages for {$packageName}:");
                foreach ($dependentConstraints as $dep => $constraint) {
                    error_log("DEBUG:   - {$dep} requires {$packageName}: {$constraint}");
                }
            }

            // Check if proposed version satisfies all constraints
            foreach ($dependentConstraints as $depPackage => $constraint) {
                $satisfies = DependencyAnalyzer::versionSatisfiesConstraint($proposedVersion, $constraint);
                if ($debug) {
                    error_log("DEBUG: Checking if {$proposedVersion} satisfies constraint '{$constraint}' from {$depPackage}: " . ($satisfies ? 'YES' : 'NO'));
                }
                if (!$satisfies) {
                    $proposedSatisfiesDependents = false;
                    if ($debug) {
                        error_log("DEBUG: Proposed version {$proposedVersion} does NOT satisfy constraint '{$constraint}' from {$depPackage}");
                        error_log("DEBUG: Rejecting {$packageName}:{$proposedVersion} because it conflicts with dependent package {$depPackage}");
                    }
                    // Track conflicting dependents for output
                    if ($conflictingDependents !== null) {
                        $conflictingDependents[$depPackage] = $constraint;
                        if ($debug) {
                            error_log("DEBUG: Tracking conflicting dependent: {$depPackage} requires {$packageName}:{$constraint}");
                        }
                    }
                    // Version doesn't satisfy dependent constraints, reject immediately
                    if ($debug) {
                        error_log("DEBUG: Rejecting {$packageName}:{$proposedVersion} immediately due to conflict with {$depPackage} (requires {$constraint})");
                    }
                    return null;
                }
            }

            if ($proposedSatisfiesDependents && $debug) {
                error_log("DEBUG: Proposed version {$proposedVersion} satisfies all dependent constraints");
            }
        } elseif ($debug) {
            error_log("DEBUG: No dependent packages found for {$packageName} in composer.lock");
        }

        // Get requirements of the proposed package version
        $packageRequirements = PackageInfoProvider::getPackageRequirements($packageName, $proposedVersion);

        if ($debug && !empty($packageRequirements)) {
            error_log("DEBUG: Package {$packageName} {$proposedVersion} requires:");
            foreach ($packageRequirements as $req => $constraint) {
                error_log("DEBUG:   - {$req}: {$constraint}");
            }
        }

        // SECOND: Check if the proposed package's requirements are compatible with installed versions
        $hasConflict = false;
        foreach ($packageRequirements as $requiredPackage => $requiredConstraint) {
            // Skip php and php-* requirements
            if ($requiredPackage === 'php' || strpos($requiredPackage, 'php-') === 0) {
                continue;
            }

            // Skip ext-* requirements
            if (strpos($requiredPackage, 'ext-') === 0) {
                continue;
            }

            // Handle "self.version" constraint
            if ($requiredConstraint === 'self.version' || $requiredConstraint === '@self') {
                $normalizedProposed = ltrim($proposedVersion, 'v');
                $requiredVersion = $normalizedProposed;

                $installedVersion = DependencyAnalyzer::getInstalledPackageVersion($requiredPackage);
                if ($installedVersion === null) {
                    continue;
                }

                $normalizedInstalled = ltrim($installedVersion, 'v');
                if ($normalizedInstalled !== $requiredVersion) {
                    if ($debug) {
                        error_log("DEBUG: Proposed package {$packageName} {$proposedVersion} requires {$requiredPackage}: {$requiredConstraint} (which means {$requiredVersion}), but installed version {$normalizedInstalled} does NOT match");
                    }
                    if ($requiredTransitiveUpdates !== null) {
                        if ($debug) {
                            error_log("DEBUG: Adding {$requiredPackage}:{$requiredVersion} to transitive updates (required by {$packageName}:{$proposedVersion} via self.version)");
                        }
                        if (!isset($requiredTransitiveUpdates[$requiredPackage])) {
                            $requiredTransitiveUpdates[$requiredPackage] = [
                                'required_by' => [],
                                'required_constraint' => $requiredConstraint . " (self.version = {$requiredVersion})",
                                'installed_version' => $installedVersion,
                                'suggested_version' => $requiredVersion
                            ];
                        }
                        $requiredTransitiveUpdates[$requiredPackage]['required_by'][] = "{$packageName}:{$proposedVersion}";
                    }
                    $hasConflict = true;
                    continue;
                }
                continue;
            }

            $installedVersion = DependencyAnalyzer::getInstalledPackageVersion($requiredPackage);
            if ($installedVersion === null) {
                continue;
            }

            $satisfies = DependencyAnalyzer::versionSatisfiesConstraint($installedVersion, $requiredConstraint);
            if ($debug) {
                error_log("DEBUG: Checking if installed version {$installedVersion} satisfies constraint {$requiredConstraint} for {$requiredPackage}: " . ($satisfies ? 'YES' : 'NO'));
            }

            if (!$satisfies) {
                if ($debug) {
                    error_log("DEBUG: Proposed package {$packageName} {$proposedVersion} requires {$requiredPackage}: {$requiredConstraint}, but installed version {$installedVersion} does NOT satisfy it");
                }
                // Check if there's an updated version of the transitive dependency
                if ($requiredTransitiveUpdates !== null) {
                    $compatibleVersion = self::findCompatibleTransitiveVersion($requiredPackage, $requiredConstraint, $installedVersion, $debug);
                    if ($compatibleVersion) {
                        if (!isset($requiredTransitiveUpdates[$requiredPackage])) {
                            $requiredTransitiveUpdates[$requiredPackage] = [
                                'required_by' => [],
                                'required_constraint' => $requiredConstraint,
                                'installed_version' => $installedVersion,
                                'suggested_version' => $compatibleVersion
                            ];
                        }
                        $requiredTransitiveUpdates[$requiredPackage]['required_by'][] = "{$packageName}:{$proposedVersion}";
                    }
                }
                $hasConflict = true;
                continue;
            }
        }

        // Return null if any conflicts were detected in package requirements
        if ($hasConflict) {
            if ($debug) {
                error_log("DEBUG: Rejecting {$packageName}:{$proposedVersion} because its requirements conflict with installed packages");
            }
            return null;
        }

        // If proposed version doesn't satisfy dependent constraints, search for compatible version
        if (!$proposedSatisfiesDependents) {
            if ($debug) {
                error_log("DEBUG: Proposed version {$proposedVersion} does not satisfy dependent constraints, searching for compatible version");
            }
            return self::findCompatibleVersionFromAvailable($packageName, $proposedVersion, $dependentConstraints, $debug, $requiredTransitiveUpdates);
        }

        // All checks passed
        if ($debug) {
            if (empty($dependentConstraints)) {
                error_log("DEBUG: No dependent packages found for {$packageName}, and requirements are compatible, using proposed version: {$proposedVersion}");
            } else {
                error_log("DEBUG: Proposed version {$proposedVersion} satisfies all dependent constraints and requirements are compatible");
            }
        }
        return $proposedVersion;
    }

    /**
     * Find compatible version from available versions
     */
    private static function findCompatibleVersionFromAvailable(
        string $packageName,
        string $proposedVersion,
        array $dependentConstraints,
        bool $debug,
        ?array &$requiredTransitiveUpdates
    ): ?string {
        $composerBin = getenv('COMPOSER_BIN') ?: 'composer';
        $phpBin = getenv('PHP_BIN') ?: 'php';

        $cmd = escapeshellarg($phpBin) . ' -d date.timezone=UTC ' . escapeshellarg($composerBin) .
               ' show ' . escapeshellarg($packageName) . ' --all --format=json 2>/dev/null';

        $output = shell_exec($cmd);
        if (!$output) {
            if ($debug) {
                error_log("DEBUG: Could not get available versions for {$packageName}, skipping compatibility check");
            }
            return null;
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

        usort($stableVersions, function($a, $b) {
            return version_compare($b, $a); // Descending order
        });

        // Find the highest version that satisfies all constraints and requirements
        foreach ($stableVersions as $version) {
            $satisfiesAll = true;

            // Check dependent constraints
            foreach ($dependentConstraints as $depPackage => $constraint) {
                if (!DependencyAnalyzer::versionSatisfiesConstraint($version, $constraint)) {
                    $satisfiesAll = false;
                    break;
                }
            }

            if (!$satisfiesAll) {
                continue;
            }

            // Check package requirements for this version
            $versionRequirements = PackageInfoProvider::getPackageRequirements($packageName, $version);
            foreach ($versionRequirements as $requiredPackage => $requiredConstraint) {
                if ($requiredPackage === 'php' || strpos($requiredPackage, 'php-') === 0 || strpos($requiredPackage, 'ext-') === 0) {
                    continue;
                }

                $installedVersion = DependencyAnalyzer::getInstalledPackageVersion($requiredPackage);
                if ($installedVersion === null) {
                    continue;
                }

                if (!DependencyAnalyzer::versionSatisfiesConstraint($installedVersion, $requiredConstraint)) {
                    if ($debug) {
                        error_log("DEBUG: Version {$version} of {$packageName} requires {$requiredPackage}:{$requiredConstraint}, but installed version {$installedVersion} does NOT satisfy it");
                    }
                    // Check for compatible transitive version
                    if ($requiredTransitiveUpdates !== null) {
                        $compatibleVersion = self::findCompatibleTransitiveVersion($requiredPackage, $requiredConstraint, $installedVersion, $debug);
                        if ($compatibleVersion) {
                            if (!isset($requiredTransitiveUpdates[$requiredPackage])) {
                                $requiredTransitiveUpdates[$requiredPackage] = [
                                    'required_by' => [],
                                    'required_constraint' => $requiredConstraint,
                                    'installed_version' => $installedVersion,
                                    'suggested_version' => $compatibleVersion
                                ];
                            }
                            $requiredTransitiveUpdates[$requiredPackage]['required_by'][] = "{$packageName}:{$version}";
                        }
                    }
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

        if ($debug) {
            error_log("DEBUG: No compatible version found for {$packageName} (proposed: {$proposedVersion})");
            if (!empty($dependentConstraints)) {
                foreach ($dependentConstraints as $depPackage => $constraint) {
                    error_log("DEBUG:   - {$depPackage} requires: {$constraint}");
                }
            }
        }
        return null;
    }

    /**
     * Find compatible transitive version
     */
    private static function findCompatibleTransitiveVersion(
        string $packageName,
        string $constraint,
        string $installedVersion,
        bool $debug
    ): ?string {
        $composerBin = getenv('COMPOSER_BIN') ?: 'composer';
        $phpBin = getenv('PHP_BIN') ?: 'php';
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
            return version_compare($b, $a); // Descending order
        });

        foreach ($stableVersions as $version) {
            if (DependencyAnalyzer::versionSatisfiesConstraint($version, $constraint)) {
                if ($debug) {
                    error_log("DEBUG: Found compatible version {$version} for {$packageName} (satisfies {$constraint})");
                }
                return $version;
            }
        }

        return null;
    }
}
