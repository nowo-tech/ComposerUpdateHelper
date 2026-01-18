<?php

declare(strict_types=1);

/**
 * Impact Analyzer
 * Analyzes the impact of updating packages on dependent packages
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

class ImpactAnalyzer
{
    /**
     * Analyze the impact of updating a package to a new version
     * Returns all packages that would be affected by the update
     *
     * @param string $packageName Package name to analyze
     * @param string $newVersion New version to analyze impact for
     * @param bool $debug Enable debug logging
     * @return array Impact analysis with 'direct' and 'transitive' affected packages
     *               Format: ['direct' => ['package1' => 'constraint1', ...], 'transitive' => ['package2' => 'constraint2', ...]]
     */
    public static function analyzeImpact(string $packageName, string $newVersion, bool $debug = false): array
    {
        $impact = [
            'direct' => [],
            'transitive' => [],
        ];

        // Get all packages that directly depend on this package
        $dependentConstraints = DependencyAnalyzer::getPackageConstraintsFromLock($packageName);

        if (empty($dependentConstraints)) {
            if ($debug) {
                error_log("DEBUG: No dependent packages found for {$packageName}, no impact analysis needed");
            }
            return $impact;
        }

        if ($debug) {
            error_log("DEBUG: Analyzing impact of updating {$packageName} to {$newVersion}");
            error_log("DEBUG: Found " . count($dependentConstraints) . " dependent package(s)");
        }

        // Check each dependent package to see if it would be affected
        foreach ($dependentConstraints as $dependentPackage => $constraint) {
            // Check if the new version satisfies the constraint
            $satisfies = DependencyAnalyzer::versionSatisfiesConstraint($newVersion, $constraint);

            if (!$satisfies) {
                // This dependent would be affected
                $impact['direct'][$dependentPackage] = $constraint;

                if ($debug) {
                    error_log("DEBUG: Direct impact: {$dependentPackage} requires {$packageName}:{$constraint}, but new version {$newVersion} does not satisfy");
                }

                // Recursively check transitive dependencies
                $transitiveImpact = self::analyzeTransitiveImpact($dependentPackage, $packageName, $debug);
                if (!empty($transitiveImpact)) {
                    $impact['transitive'] = array_merge($impact['transitive'], $transitiveImpact);
                }
            }
        }

        // Remove duplicates from transitive (in case multiple direct dependents lead to same transitive)
        $impact['transitive'] = array_values(array_unique($impact['transitive']));

        if ($debug) {
            error_log("DEBUG: Impact analysis complete: " . count($impact['direct']) . " direct, " . count($impact['transitive']) . " transitive");
        }

        return $impact;
    }

    /**
     * Analyze transitive impact (packages that depend on the affected dependent)
     *
     * @param string $dependentPackage Package that directly depends on the updated package
     * @param string $originalPackage Original package being updated (to avoid cycles)
     * @param bool $debug Enable debug logging
     * @param int $depth Current recursion depth (to prevent infinite loops)
     * @param int $maxDepth Maximum recursion depth
     * @return array Transitive affected packages
     */
    private static function analyzeTransitiveImpact(
        string $dependentPackage,
        string $originalPackage,
        bool $debug = false,
        int $depth = 0,
        int $maxDepth = 5
    ): array {
        if ($depth >= $maxDepth) {
            if ($debug) {
                error_log("DEBUG: Max depth reached for transitive impact analysis of {$dependentPackage}");
            }
            return [];
        }

        // Get installed version of the dependent package
        $dependentVersion = DependencyAnalyzer::getInstalledPackageVersion($dependentPackage);
        if (!$dependentVersion) {
            return [];
        }

        // Get all packages that depend on this dependent package
        $transitiveDependents = DependencyAnalyzer::getPackageConstraintsFromLock($dependentPackage);

        if (empty($transitiveDependents)) {
            return [];
        }

        $transitiveImpact = [];

        foreach ($transitiveDependents as $transitivePackage => $constraint) {
            // Skip if this is the original package (circular reference)
            if ($transitivePackage === $originalPackage) {
                continue;
            }

            // Check if the current version of dependent satisfies the transitive constraint
            $satisfies = DependencyAnalyzer::versionSatisfiesConstraint($dependentVersion, $constraint);

            // If it satisfies, the transitive package might be affected if the dependent needs to be updated
            // For now, we'll add it to show the dependency chain
            // A more sophisticated analysis would check if updating the dependent would break the transitive
            if ($satisfies) {
                $transitiveImpact[] = $transitivePackage;

                if ($debug) {
                    error_log("DEBUG: Transitive impact (depth {$depth}): {$transitivePackage} depends on {$dependentPackage}");
                }
            }
        }

        return $transitiveImpact;
    }

    /**
     * Format impact analysis for output
     *
     * @param array $impact Impact analysis result from analyzeImpact()
     * @param string $packageName Package name being analyzed
     * @param string $newVersion New version
     * @return array Formatted impact information
     */
    public static function formatImpactForOutput(array $impact, string $packageName, string $newVersion): array
    {
        $formatted = [
            'package' => $packageName,
            'new_version' => $newVersion,
            'direct_affected' => [],
            'transitive_affected' => [],
            'total_affected' => 0,
        ];

        // Format direct affected packages
        foreach ($impact['direct'] as $dependentPackage => $constraint) {
            $formatted['direct_affected'][] = [
                'package' => $dependentPackage,
                'constraint' => $constraint,
                'reason' => "requires {$packageName}:{$constraint}",
            ];
        }

        // Format transitive affected packages
        foreach ($impact['transitive'] as $transitivePackage) {
            $formatted['transitive_affected'][] = [
                'package' => $transitivePackage,
                'reason' => "transitively depends on affected packages",
            ];
        }

        $formatted['total_affected'] = count($formatted['direct_affected']) + count($formatted['transitive_affected']);

        return $formatted;
    }
}
