<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper;

use Composer\IO\IOInterface;
use Composer\Script\Event;

/**
 * Static installer for composer scripts.
 * Can be used directly in composer.json scripts section.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 *
 * @see    https://github.com/HecFranco
 */
class Installer
{
    /**
     * Get the octal permission mode compatible with the current PHP version.
     * Uses explicit octal notation (0o755) for PHP 8.1+, implicit (0755) for older versions.
     *
     * @return int The permission mode
     */
    private static function getChmodMode(): int
    {
        // Explicit octal notation (0o755) was introduced in PHP 8.1
        // Since PHP parses the entire file before execution, we can't use 0o755 literal
        // Instead, we calculate the value: 0755 = 7*64 + 5*8 + 5 = 493
        // For PHP 8.1+, we could use 0o755, but to maintain compatibility with 7.4/8.0,
        // we use the decimal equivalent or calculate from octal string
        return octdec('755');
    }

    /**
     * Install files to the project root.
     *
     * @param Event $event The script event
     */
    public static function install(Event $event): void
    {
        $io = $event->getIO();
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $projectDir = dirname((string) $vendorDir);
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';

        // If package is not in vendor (development mode), use current directory
        if (!is_dir($packageDir)) {
            $packageDir = __DIR__ . '/..';
        }

        $files = [
            'bin/generate-composer-require.sh' => 'generate-composer-require.sh',
        ];

        foreach ($files as $source => $dest) {
            $sourcePath = $packageDir . '/' . $source;
            $destPath = $projectDir . '/' . $dest;

            if (!file_exists($sourcePath)) {
                continue;
            }

            if (file_exists($destPath)) {
                if (md5_file($sourcePath) === md5_file($destPath)) {
                    continue;
                }

                $io->write(sprintf('<info>Updating %s</info>', $dest));
            } else {
                $io->write(sprintf('<info>Installing %s</info>', $dest));
            }

            copy($sourcePath, $destPath);
            chmod($destPath, self::getChmodMode());
        }

        // Create YAML config file only if it doesn't exist
        $yamlSource = $packageDir . '/bin/generate-composer-require.yaml';
        $yamlDest = $projectDir . '/generate-composer-require.yaml';

        if (!file_exists($yamlDest) && file_exists($yamlSource)) {
            $io->write('<info>Creating generate-composer-require.yaml</info>');
            copy($yamlSource, $yamlDest);
        }

        // Check for old TXT file and migrate if exists (upgrade scenario)
        $oldIgnoreTxt = $projectDir . '/generate-composer-require.ignore.txt';
        if (file_exists($oldIgnoreTxt) && !file_exists($yamlDest)) {
            $io->write('<info>Migrating configuration from TXT to YAML format</info>');
            self::migrateTxtToYaml($oldIgnoreTxt, $yamlDest, $io);
            // Delete the old TXT file after successful migration
            if (file_exists($yamlDest)) {
                unlink($oldIgnoreTxt);
                $io->write('<info>Removed old generate-composer-require.ignore.txt file</info>');
            }
        }

        // Update .gitignore to exclude installed files
        self::updateGitignore($projectDir, $io);
    }

    /**
     * Migrate old TXT configuration file to YAML format.
     *
     * @param string      $txtPath  Path to the old TXT file
     * @param string      $yamlPath Path to the new YAML file
     * @param IOInterface $io       The IO interface
     */
    private static function migrateTxtToYaml(string $txtPath, string $yamlPath, IOInterface $io): void
    {
        $content = file_get_contents($txtPath);
        $lines = explode("\n", $content);

        $packages = [];
        foreach ($lines as $line) {
            $line = trim($line);
            // Skip comments and empty lines
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            // Add package to ignore list
            if (!empty($line)) {
                $packages[] = $line;
            }
        }

        // Create YAML content
        $yamlContent = "# Composer Update Helper Configuration\n";
        $yamlContent .= "# Configuration file for ignored packages during composer update suggestions\n";
        $yamlContent .= "# Migrated from generate-composer-require.ignore.txt\n\n";
        $yamlContent .= "# List of packages to ignore during update\n";
        $yamlContent .= "# Ignored packages will still be displayed in the output with their available versions,\n";
        $yamlContent .= "# but won't be included in the composer require commands.\n";
        $yamlContent .= "ignore:\n";

        if (empty($packages)) {
            $yamlContent .= "  # Add packages to ignore (one per line)\n";
            $yamlContent .= "  # - doctrine/orm\n";
            $yamlContent .= "  # - symfony/security-bundle\n";
        } else {
            foreach ($packages as $package) {
                $yamlContent .= "  - {$package}\n";
            }
        }

        file_put_contents($yamlPath, $yamlContent);
        $io->write(sprintf('<info>Configuration migrated to %s</info>', basename($yamlPath)));
    }

    /**
     * Update .gitignore to exclude Composer Update Helper files.
     *
     * @param string      $projectDir The project root directory
     * @param IOInterface $io         The IO interface
     */
    private static function updateGitignore(string $projectDir, IOInterface $io): void
    {
        $gitignorePath = $projectDir . '/.gitignore';
        $entriesToAdd = [
            'generate-composer-require.sh',
            'generate-composer-require.yaml',
        ];

        // Also remove old TXT entry if it exists (for migration)
        $entriesToRemove = [
            'generate-composer-require.ignore.txt',
        ];

        $content = '';
        $lines = [];

        if (file_exists($gitignorePath)) {
            $content = file_get_contents($gitignorePath);
            $lines = explode("\n", $content);
        }

        $updated = false;
        $existingEntries = array_map('trim', $lines);

        // Remove old TXT entry if it exists
        foreach ($entriesToRemove as $entry) {
            $key = array_search($entry, $existingEntries, true);
            if ($key !== false) {
                unset($lines[$key]);
                $existingEntries = array_map('trim', $lines);
                $updated = true;
            }
        }

        // Add new entries
        foreach ($entriesToAdd as $entry) {
            if (!in_array($entry, $existingEntries, true)) {
                // Add a comment if this is the first entry and file exists
                if (!$updated && file_exists($gitignorePath) && !empty($content)) {
                    $trimmedContent = trim($content);
                    if ($trimmedContent !== '' && substr($trimmedContent, -1) !== "\n") {
                        $lines[] = '';
                    }
                }
                // Add comment header if this is the first Composer Update Helper entry
                if (!$updated && !in_array('# Composer Update Helper', $existingEntries, true)) {
                    $lines[] = '# Composer Update Helper';
                }
                $lines[] = $entry;
                $updated = true;
            }
        }

        if ($updated) {
            file_put_contents($gitignorePath, implode("\n", $lines) . "\n");
            $io->write('<info>Updated .gitignore to exclude Composer Update Helper files</info>');
        }
    }

    /**
     * Uninstall files from the project root.
     *
     * @param Event $event The script event
     */
    public static function uninstall(Event $event): void
    {
        $io = $event->getIO();
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $projectDir = dirname((string) $vendorDir);

        $file = $projectDir . '/generate-composer-require.sh';

        if (file_exists($file)) {
            $io->write('<info>Removing generate-composer-require.sh</info>');
            unlink($file);
        }
    }
}
