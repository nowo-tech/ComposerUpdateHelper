<?php

declare(strict_types=1);

/**
 * Alternative Package Finder
 * Finds alternative packages when updates are blocked by conflicts
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

class AlternativePackageFinder
{
    /**
     * Find alternative packages for a given package
     * Uses Packagist API to search for similar packages or suggested replacements
     *
     * @param string $packageName Package name to find alternatives for
     * @param bool $debug Enable debug logging
     * @return array|null Returns array of alternative packages or null on error
     *                    Format: ['alternatives' => [['name' => 'vendor/package', 'description' => '...', 'downloads' => ...], ...]]
     */
    public static function findAlternatives(string $packageName, bool $debug = false): ?array
    {
        // First check if package is abandoned and has a replacement
        $abandonedInfo = AbandonedPackageDetector::isPackageAbandoned($packageName, $debug);
        if ($abandonedInfo && $abandonedInfo['abandoned'] && $abandonedInfo['replacement']) {
            if ($debug) {
                error_log("DEBUG: Package {$packageName} is abandoned with replacement: {$abandonedInfo['replacement']}");
            }
            
            // Get replacement package info
            $replacementInfo = self::getPackageInfo($abandonedInfo['replacement'], $debug);
            if ($replacementInfo) {
                return [
                    'alternatives' => [$replacementInfo],
                    'reason' => 'abandoned_replacement'
                ];
            }
        }

        // Search for similar packages using Packagist search API
        $alternatives = self::searchSimilarPackages($packageName, $debug);
        
        if (!empty($alternatives)) {
            return [
                'alternatives' => $alternatives,
                'reason' => 'similar_packages'
            ];
        }

        return null;
    }

    /**
     * Search for similar packages using Packagist search API
     * Uses keywords extracted from package name and description
     *
     * @param string $packageName Package name to search alternatives for
     * @param bool $debug Enable debug logging
     * @return array Array of alternative packages (limited to top 3 most relevant)
     */
    private static function searchSimilarPackages(string $packageName, bool $debug = false): array
    {
        // Extract keywords from package name (e.g., "symfony/security-bundle" -> "security", "bundle")
        $parts = explode('/', $packageName);
        $packagePart = end($parts);
        
        // Extract meaningful keywords (skip common words like "bundle", "library", "package")
        $keywords = self::extractKeywords($packagePart);
        
        if (empty($keywords)) {
            return [];
        }

        // Use Packagist search API
        $searchQuery = implode(' ', $keywords);
        $url = "https://packagist.org/search.json?q=" . urlencode($searchQuery) . "&per_page=10";
        
        if ($debug) {
            error_log("DEBUG: Searching for alternatives to {$packageName} using query: {$searchQuery}");
            error_log("DEBUG: Packagist search URL: {$url}");
        }

        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'Composer Update Helper',
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        if (!$response) {
            if ($debug) {
                error_log("DEBUG: Could not fetch Packagist search results for {$packageName}");
            }
            return [];
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['results']) || !is_array($data['results'])) {
            if ($debug) {
                error_log("DEBUG: Invalid Packagist search response for {$packageName}");
            }
            return [];
        }

        // Filter out the original package and get top alternatives
        $alternatives = [];
        foreach ($data['results'] as $result) {
            if (!isset($result['name'])) {
                continue;
            }
            
            // Skip the original package itself
            if ($result['name'] === $packageName) {
                continue;
            }

            // Limit to top 3 most relevant alternatives
            if (count($alternatives) >= 3) {
                break;
            }

            $alternatives[] = [
                'name' => $result['name'],
                'description' => $result['description'] ?? '',
                'downloads' => $result['downloads'] ?? 0,
                'favers' => $result['favers'] ?? 0,
            ];
        }

        if ($debug) {
            error_log("DEBUG: Found " . count($alternatives) . " alternative(s) for {$packageName}");
        }

        return $alternatives;
    }

    /**
     * Extract meaningful keywords from package name
     *
     * @param string $packagePart Package name part (after vendor/)
     * @return array Array of keywords
     */
    private static function extractKeywords(string $packagePart): array
    {
        // Remove common suffixes/prefixes
        $commonWords = ['bundle', 'library', 'package', 'component', 'bridge', 'adapter', 'plugin', 'extension'];
        
        // Split by common separators (hyphen, underscore, camelCase)
        $words = preg_split('/[-_]+|(?=[A-Z])/', $packagePart);
        
        $keywords = [];
        foreach ($words as $word) {
            $word = strtolower(trim($word));
            if (empty($word)) {
                continue;
            }
            
            // Skip common words and very short words
            if (in_array($word, $commonWords) || strlen($word) < 3) {
                continue;
            }
            
            $keywords[] = $word;
        }

        // Return top 2-3 most relevant keywords
        return array_slice(array_unique($keywords), 0, 3);
    }

    /**
     * Get package information from Packagist
     *
     * @param string $packageName Package name
     * @param bool $debug Enable debug logging
     * @return array|null Package information or null
     */
    private static function getPackageInfo(string $packageName, bool $debug = false): ?array
    {
        $url = "https://packagist.org/packages/{$packageName}.json";
        
        if ($debug) {
            error_log("DEBUG: Fetching package info for {$packageName} from {$url}");
        }

        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'Composer Update Helper',
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['package'])) {
            return null;
        }

        $package = $data['package'];
        
        return [
            'name' => $packageName,
            'description' => $package['description'] ?? '',
            'downloads' => $package['downloads']['total'] ?? 0,
            'favers' => $package['favers'] ?? 0,
        ];
    }
}
