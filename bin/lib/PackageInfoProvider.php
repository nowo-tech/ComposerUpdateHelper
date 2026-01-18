<?php

declare(strict_types=1);

/**
 * Package Info Provider
 * Provides package information from external APIs (Packagist, GitHub)
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

class PackageInfoProvider
{
    /**
     * Get package requirements from Packagist
     *
     * @param string $packageName Package name
     * @param string $version Version to check
     * @return array Package requirements
     */
    public static function getPackageRequirements(string $packageName, string $version): array
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
            // Fallback: try composer show
            return self::getPackageRequirementsFromComposer($packageName, $version);
        }

        $data = json_decode($json, true);
        if (!$data || !isset($data['package']['versions'])) {
            // Fallback: try composer show
            return self::getPackageRequirementsFromComposer($packageName, $version);
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
        return self::getPackageRequirementsFromComposer($packageName, $version);
    }

    /**
     * Get package requirements from composer show (fallback)
     *
     * @param string $packageName Package name
     * @param string $version Version to check
     * @return array Package requirements
     */
    public static function getPackageRequirementsFromComposer(string $packageName, string $version): array
    {
        $composerBin = getenv('COMPOSER_BIN') ?: 'composer';
        $phpBin = getenv('PHP_BIN') ?: 'php';

        $cmd = escapeshellarg($phpBin) . ' -d date.timezone=UTC ' . escapeshellarg($composerBin) .
               ' show ' . escapeshellarg($packageName . ':' . $version) . ' --format=json 2>/dev/null';

        $output = shell_exec($cmd);
        if (!$output) {
            return [];
        }

        $data = json_decode($output, true);
        if (!$data || !isset($data['requires'])) {
            return [];
        }

        return $data['requires'] ?? [];
    }

    /**
     * Get GitHub repository URL from Packagist
     *
     * @param string $packageName Package name
     * @return string|null GitHub repository (format: user/repo) or null
     */
    public static function getGitHubRepoFromPackagist(string $packageName): ?string
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

    /**
     * Get release information from GitHub
     *
     * @param string $githubRepo GitHub repository (format: user/repo)
     * @param string $version Version to check
     * @return array|null Release information or null
     */
    public static function getReleaseInfo(string $githubRepo, string $version): ?array
    {
        if (!$githubRepo) {
            return null;
        }

        // Normalize version (remove 'v' prefix if present)
        $normalizedVersion = ltrim($version, 'v');

        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'Composer Update Helper',
                'header' => 'Accept: application/vnd.github.v3+json',
            ]
        ]);

        // Try to get release by tag
        $url = "https://api.github.com/repos/{$githubRepo}/releases/tags/v{$normalizedVersion}";
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
}
