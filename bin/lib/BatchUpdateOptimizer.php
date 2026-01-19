<?php

declare(strict_types=1);

/**
 * Batch Update Optimizer
 * Optimizes the order of package updates to minimize dependency resolution steps
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

class BatchUpdateOptimizer
{
    /**
     * Optimize update order by analyzing dependencies between packages to update
     *
     * @param array $packagesToUpdate Array of package strings (format: "package-name:version")
     * @param bool $debug Enable debug logging
     * @return array Optimized batches (array of arrays, each batch contains packages that can be updated together)
     */
    public static function optimizeUpdateOrder(array $packagesToUpdate, bool $debug = false): array
    {
        if (empty($packagesToUpdate) || count($packagesToUpdate) <= 1) {
            // No optimization needed for 0 or 1 package
            return empty($packagesToUpdate) ? [] : [$packagesToUpdate];
        }

        // Build dependency graph between packages to update
        $graph = self::buildDependencyGraph($packagesToUpdate, $debug);

        if (empty($graph)) {
            // No dependencies found between packages, can update all together
            return [$packagesToUpdate];
        }

        // Perform topological sort
        $sorted = self::topologicalSort($graph, $packagesToUpdate, $debug);

        // Group by dependency level (packages at the same level can be updated together)
        $batches = self::groupByDependencyLevel($sorted, $graph, $packagesToUpdate);

        if ($debug) {
            error_log("DEBUG: Batch optimization: " . count($packagesToUpdate) . " packages optimized into " . count($batches) . " batch(es)");
        }

        return $batches;
    }

    /**
     * Build dependency graph between packages to update
     *
     * @param array $packagesToUpdate Array of package strings (format: "package-name:version")
     * @param bool $debug Enable debug logging
     * @return array Graph structure: ['package-name' => ['depends-on' => ['other-package-name' => true]]]
     */
    private static function buildDependencyGraph(array $packagesToUpdate, bool $debug = false): array
    {
        $graph = [];
        $packageNames = [];

        // Extract package names
        foreach ($packagesToUpdate as $packageString) {
            $parts = explode(':', $packageString, 2);
            $packageName = $parts[0];
            $packageNames[$packageName] = $packageString;
            $graph[$packageName] = ['depends-on' => []];
        }

        // Check dependencies between packages using composer.lock (faster than API calls)
        // We check installed package requirements from lock file to build the graph
        if (!file_exists('composer.lock')) {
            // No lock file, can't build graph efficiently
            return [];
        }

        $lock = @json_decode(file_get_contents('composer.lock'), true);
        if (!$lock) {
            return [];
        }

        $allLockPackages = array_merge(
            $lock['packages'] ?? [],
            $lock['packages-dev'] ?? []
        );

        // Build a map of installed packages and their requirements
        $installedRequirements = [];
        foreach ($allLockPackages as $pkg) {
            if (!isset($pkg['name'])) {
                continue;
            }
            $installedRequirements[$pkg['name']] = array_merge(
                $pkg['require'] ?? [],
                $pkg['require-dev'] ?? []
            );
        }

        // Check dependencies between packages to update
        foreach ($packageNames as $packageName => $packageString) {
            // Use installed requirements as proxy (packages in update list are already installed)
            // New version requirements should be similar, differences will be caught by composer resolver
            $requirements = $installedRequirements[$packageName] ?? [];

            if (empty($requirements)) {
                continue;
            }

            // Check if any of the requirements are in our update list
            foreach ($requirements as $requiredPackage => $constraint) {
                if (isset($packageNames[$requiredPackage])) {
                    // This package depends on another package we're updating
                    $graph[$packageName]['depends-on'][$requiredPackage] = true;
                    if ($debug) {
                        error_log("DEBUG: Dependency edge: {$packageName} depends on {$requiredPackage}");
                    }
                }
            }
        }

        return $graph;
    }

    /**
     * Perform topological sort to determine update order
     *
     * @param array $graph Dependency graph
     * @param array $packagesToUpdate Original packages list
     * @param bool $debug Enable debug logging
     * @return array Topologically sorted package names
     */
    private static function topologicalSort(array $graph, array $packagesToUpdate, bool $debug = false): array
    {
        $sorted = [];
        $visited = [];
        $tempMark = [];

        // Initialize all packages as not visited
        foreach ($graph as $packageName => $_) {
            if (!isset($visited[$packageName])) {
                self::topologicalVisit($packageName, $graph, $visited, $tempMark, $sorted, $debug);
            }
        }

        // Reverse to get correct order (dependencies first)
        return array_reverse($sorted);
    }

    /**
     * Visit node for topological sort (DFS)
     *
     * @param string $packageName Package to visit
     * @param array $graph Dependency graph
     * @param array $visited Visited nodes
     * @param array $tempMark Temporarily marked nodes (for cycle detection)
     * @param array $sorted Sorted result
     * @param bool $debug Enable debug logging
     */
    private static function topologicalVisit(
        string $packageName,
        array $graph,
        array &$visited,
        array &$tempMark,
        array &$sorted,
        bool $debug = false
    ): void {
        if (isset($tempMark[$packageName])) {
            // Cycle detected - skip to avoid infinite loop
            if ($debug) {
                error_log("DEBUG: Cycle detected in dependency graph for {$packageName}, skipping");
            }
            return;
        }

        if (isset($visited[$packageName])) {
            return;
        }

        $tempMark[$packageName] = true;

        // Visit all dependencies first
        if (isset($graph[$packageName]['depends-on'])) {
            foreach ($graph[$packageName]['depends-on'] as $dependency => $_) {
                if (isset($graph[$dependency])) {
                    self::topologicalVisit($dependency, $graph, $visited, $tempMark, $sorted, $debug);
                }
            }
        }

        unset($tempMark[$packageName]);
        $visited[$packageName] = true;
        $sorted[] = $packageName;
    }

    /**
     * Group sorted packages by dependency level
     * Packages at the same level have no dependencies on each other and can be updated together
     *
     * @param array $sorted Topologically sorted package names
     * @param array $graph Dependency graph
     * @param array $packagesToUpdate Original packages list (for mapping back to full strings)
     * @return array Batches grouped by dependency level
     */
    private static function groupByDependencyLevel(array $sorted, array $graph, array $packagesToUpdate): array
    {
        $batches = [];
        $packageMap = [];

        // Create map of package name to full package string
        foreach ($packagesToUpdate as $packageString) {
            $parts = explode(':', $packageString, 2);
            $packageMap[$parts[0]] = $packageString;
        }

        // Assign levels based on longest dependency path
        $levels = [];
        foreach ($sorted as $packageName) {
            $level = 0;
            if (isset($graph[$packageName]['depends-on']) && !empty($graph[$packageName]['depends-on'])) {
                // Calculate level based on max dependency level + 1
                foreach ($graph[$packageName]['depends-on'] as $dependency => $_) {
                    if (isset($levels[$dependency])) {
                        $level = max($level, $levels[$dependency] + 1);
                    }
                }
            }
            $levels[$packageName] = $level;
        }

        // Group by level
        foreach ($levels as $packageName => $level) {
            if (!isset($batches[$level])) {
                $batches[$level] = [];
            }
            $batches[$level][] = $packageMap[$packageName];
        }

        // Sort by level and return as array
        ksort($batches);
        return array_values($batches);
    }
}
