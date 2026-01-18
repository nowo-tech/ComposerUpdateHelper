<?php

declare(strict_types=1);

/**
 * Abandoned Package Detector
 * Detects abandoned packages via Packagist API
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

class AbandonedPackageDetector
{
    /**
     * Check if a package is abandoned via Packagist API
     *
     * @param string $packageName Package name to check
     * @param bool $debug Enable debug logging
     * @return array|null Returns array with 'abandoned' (bool) and 'replacement' (string|null), or null on error
     */
    public static function isPackageAbandoned(string $packageName, bool $debug = false): ?array
    {
        $url = "https://packagist.org/packages/{$packageName}.json";

        if ($debug) {
            error_log("DEBUG: Checking abandoned status for {$packageName} at {$url}");
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
                error_log("DEBUG: Could not fetch Packagist data for {$packageName}");
            }
            return null;
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['package'])) {
            if ($debug) {
                error_log("DEBUG: Invalid Packagist response for {$packageName}");
            }
            return null;
        }

        $package = $data['package'];
        $abandoned = isset($package['abandoned']) && $package['abandoned'] !== false;
        $replacement = $abandoned && is_string($package['abandoned']) ? $package['abandoned'] : null;

        if ($debug) {
            error_log("DEBUG: Package {$packageName} - abandoned: " . ($abandoned ? 'YES' : 'NO') .
                      ($replacement ? " (replacement: {$replacement})" : ""));
        }

        return [
            'abandoned' => $abandoned,
            'replacement' => $replacement
        ];
    }
}
