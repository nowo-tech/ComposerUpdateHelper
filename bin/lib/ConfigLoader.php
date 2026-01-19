<?php

declare(strict_types=1);

/**
 * Configuration Loader
 * Handles reading configuration from YAML and TXT files
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

class ConfigLoader
{
    /**
     * Read packages from YAML file
     */
    public static function readPackagesFromYaml(string $yamlPath, string $section): array
    {
        if (!file_exists($yamlPath)) {
            return [];
        }

        $content = file_get_contents($yamlPath);
        if ($content === false) {
            return [];
        }

        $packages = [];
        $lines = explode("\n", $content);
        $inSection = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            $originalLine = $line;

            // Skip empty lines and pure comment lines
            if (empty($trimmedLine) || strpos($trimmedLine, '#') === 0) {
                continue;
            }

            // Check for section header
            if (preg_match('/^' . preg_quote($section, '/') . ':\s*$/', $trimmedLine)) {
                $inSection = true;
                continue;
            }

            // Check for other section headers (end current section)
            if ($inSection && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*:\s*$/', $trimmedLine)) {
                $inSection = false;
                continue;
            }

            // If we're in the section and it's a package line
            if ($inSection && preg_match('/^\s*-\s*(.+)$/', $trimmedLine, $matches)) {
                $package = trim($matches[1]);
                if (!empty($package)) {
                    $packages[] = $package;
                }
            }
        }

        return $packages;
    }

    /**
     * Read a configuration value from YAML file
     *
     * @param string $yamlPath Path to YAML file
     * @param string $key Configuration key
     * @param string|int|float|bool|null $default Default value if key not found
     * @return string|int|float|bool|null Configuration value or default
     *                                    Returns: bool for 'true'/'false', int/float for numbers, string for text, null if not found
     */
    public static function readConfigValue(string $yamlPath, string $key, $default = null): string|int|float|bool|null
    {
        if (!file_exists($yamlPath)) {
            return $default;
        }

        $content = file_get_contents($yamlPath);
        if ($content === false) {
            return $default;
        }

        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            // Skip empty lines and pure comment lines
            if (empty($trimmedLine) || strpos($trimmedLine, '#') === 0) {
                continue;
            }

            // Check for key: value pattern
            if (preg_match('/^' . preg_quote($key, '/') . ':\s*(.+)$/', $trimmedLine, $matches)) {
                $value = trim($matches[1]);
                // Handle boolean values
                if (strtolower($value) === 'true') {
                    return true;
                }
                if (strtolower($value) === 'false') {
                    return false;
                }
                // Handle numeric values
                if (is_numeric($value)) {
                    return $value + 0; // Convert to int or float
                }
                // Return as string
                return $value;
            }
        }

        return $default;
    }

    /**
     * Read packages from TXT file (backward compatibility)
     */
    public static function readPackagesFromTxt(string $txtPath): array
    {
        if (!file_exists($txtPath)) {
            return [];
        }

        $content = file_get_contents($txtPath);
        if ($content === false) {
            return [];
        }

        $packages = [];
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            $line = trim($line);
            // Skip comments and empty lines
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            if (!empty($line)) {
                $packages[] = $line;
            }
        }

        return $packages;
    }
}
