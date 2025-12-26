<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\{Event, ScriptEvents};

/**
 * Composer plugin that installs the generate-composer-require script.
 * Works with any PHP project (Symfony, Laravel, Yii, CodeIgniter, etc.)
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 *
 * @see    https://github.com/HecFranco
 */
class Plugin implements PluginInterface, EventSubscriberInterface
{
    /** @var Composer The Composer instance */
    private Composer $composer;

    /** @var IOInterface The IO interface */
    private IOInterface $io;

    /**
     * Get the octal permission mode compatible with the current PHP version.
     * Uses explicit octal notation (0o755) for PHP 8.1+, implicit (0755) for older versions.
     *
     * @return int The permission mode
     */
    private function getChmodMode(): int
    {
        // Explicit octal notation (0o755) was introduced in PHP 8.1
        // Since PHP parses the entire file before execution, we can't use 0o755 literal
        // Instead, we calculate the value: 0755 = 7*64 + 5*8 + 5 = 493
        // For PHP 8.1+, we could use 0o755, but to maintain compatibility with 7.4/8.0,
        // we use the decimal equivalent or calculate from octal string
        return octdec('755');
    }

    /**
     * Activate the plugin.
     *
     * @param Composer    $composer The Composer instance
     * @param IOInterface $io       The IO interface
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * Deactivate the plugin.
     *
     * @param Composer    $composer The Composer instance
     * @param IOInterface $io       The IO interface
     */
    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * Uninstall the plugin.
     *
     * @param Composer    $composer The Composer instance
     * @param IOInterface $io       The IO interface
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {
        $this->removeFiles($io);
    }

    /**
     * Get the subscribed events.
     *
     * @return array<string, string> The subscribed events
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'onPostInstall',
            ScriptEvents::POST_UPDATE_CMD => 'onPostUpdate',
        ];
    }

    /**
     * Handle post-install command event.
     *
     * @param Event $event The script event
     */
    public function onPostInstall(Event $event): void
    {
        $this->installFiles($event->getIO(), false);
    }

    /**
     * Handle post-update command event.
     *
     * @param Event $event The script event
     */
    public function onPostUpdate(Event $event): void
    {
        // Check for migration and update .gitignore on updates
        // Also check if YAML config needs to be created
        $this->handleConfigMigration($event->getIO());
        $this->updateGitignoreOnUpdate($event->getIO());
    }

    /**
     * Install files to the project root.
     * Only installs files if they don't exist (first installation only).
     *
     * @param IOInterface $io          The IO interface
     * @param bool        $forceUpdate Force update even if files exist
     */
    private function installFiles(IOInterface $io, bool $forceUpdate = false): void
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
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
                $io->writeError(sprintf('<warning>Source file not found: %s</warning>', $sourcePath));
                continue;
            }

            // Only install if file doesn't exist (first installation)
            if (file_exists($destPath) && !$forceUpdate) {
                continue;
            }

            if (file_exists($destPath)) {
                $io->write(sprintf('<info>Updating %s</info>', $dest));
            } else {
                $io->write(sprintf('<info>Installing %s</info>', $dest));
            }

            copy($sourcePath, $destPath);
            chmod($destPath, $this->getChmodMode());
        }

        // Handle configuration migration and creation
        $this->handleConfigMigration($io);

        // Update .gitignore to exclude installed files
        $this->updateGitignore($projectDir, $io);
    }

    /**
     * Handle configuration migration (TXT to YAML) and creation of YAML config.
     *
     * @param IOInterface $io The IO interface
     */
    private function handleConfigMigration(IOInterface $io): void
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        $projectDir = dirname((string) $vendorDir);
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';

        // If package is not in vendor (development mode), use current directory
        if (!is_dir($packageDir)) {
            $packageDir = __DIR__ . '/..';
        }

        $oldIgnoreTxt = $projectDir . '/generate-composer-require.ignore.txt';
        $newIgnoreYaml = $projectDir . '/generate-composer-require.yaml';
        $yamlSource = $packageDir . '/bin/generate-composer-require.yaml';

        // Migrate old TXT file to YAML if it exists
        // Migrate if: TXT exists AND (YAML doesn't exist OR YAML is empty/template only OR packages match)
        if (file_exists($oldIgnoreTxt)) {
            // Read packages from TXT
            $txtContent = file_get_contents($oldIgnoreTxt);
            $txtLines = explode("\n", $txtContent);
            $txtPackages = [];
            foreach ($txtLines as $line) {
                $line = trim($line);
                if (!empty($line) && strpos($line, '#') !== 0) {
                    $txtPackages[] = $line;
                }
            }

            $shouldMigrate = false;
            $shouldDeleteTxt = false;

            if (!file_exists($newIgnoreYaml)) {
                // YAML doesn't exist, migrate
                $shouldMigrate = true;
            } elseif ($this->isYamlEmptyOrTemplate($newIgnoreYaml, $yamlSource)) {
                // YAML exists but is empty or just the template, safe to migrate
                $shouldMigrate = true;
            } else {
                // YAML exists and has content, check if TXT packages are already in ignore section
                $yamlContent = file_get_contents($newIgnoreYaml);
                $yamlPackages = $this->extractPackagesFromYamlIgnoreSection($yamlContent);

                // Verify packages match (order doesn't matter)
                $txtPackagesSorted = array_unique(array_filter($txtPackages));
                $yamlPackagesSorted = array_unique(array_filter($yamlPackages));
                sort($txtPackagesSorted);
                sort($yamlPackagesSorted);

                if ($txtPackagesSorted === $yamlPackagesSorted) {
                    // Packages already migrated, just delete TXT
                    $shouldDeleteTxt = true;
                } else {
                    // Packages don't match, need to migrate (merge TXT into YAML)
                    $shouldMigrate = true;
                }
            }

            if ($shouldMigrate) {
                $io->write('<info>Migrating configuration from TXT to YAML format</info>');
                $this->migrateTxtToYaml($oldIgnoreTxt, $newIgnoreYaml, $io);

                // Verify migration was successful before deleting TXT
                if (file_exists($newIgnoreYaml)) {
                    $yamlContent = file_get_contents($newIgnoreYaml);
                    $yamlPackages = $this->extractPackagesFromYamlIgnoreSection($yamlContent);

                    // Verify packages match (order doesn't matter)
                    $txtPackagesSorted = array_unique(array_filter($txtPackages));
                    $yamlPackagesSorted = array_unique(array_filter($yamlPackages));
                    sort($txtPackagesSorted);
                    sort($yamlPackagesSorted);

                    if ($txtPackagesSorted === $yamlPackagesSorted) {
                        // Migration verified, safe to delete TXT
                        unlink($oldIgnoreTxt);
                        $io->write('<info>Removed old generate-composer-require.ignore.txt file</info>');
                    } else {
                        $io->writeError('<warning>Migration verification failed. TXT file preserved for safety.</warning>');
                    }
                }
            } elseif ($shouldDeleteTxt) {
                // Packages already in YAML, just delete TXT
                unlink($oldIgnoreTxt);
                $io->write('<info>Removed old generate-composer-require.ignore.txt file</info>');
            }
        }

        // Create YAML config file only if it doesn't exist (don't overwrite user's config)
        if (!file_exists($newIgnoreYaml) && file_exists($yamlSource)) {
            $io->write('<info>Creating generate-composer-require.yaml</info>');
            copy($yamlSource, $newIgnoreYaml);
        }
    }

    /**
     * Check if YAML file is empty or contains only the template (no user packages).
     *
     * @param string $yamlPath     Path to the YAML file
     * @param string $templatePath Path to the template YAML file
     *
     * @return bool True if YAML is empty or template-only
     */
    private function isYamlEmptyOrTemplate(string $yamlPath, string $templatePath): bool
    {
        if (!file_exists($yamlPath)) {
            return true;
        }

        $yamlContent = file_get_contents($yamlPath);
        $yamlContent = trim($yamlContent);

        // If file is empty, it's safe to migrate
        if (empty($yamlContent)) {
            return true;
        }

        // Check if YAML has any actual packages (not just comments)
        // Look for lines with "  - " that are not commented out in both ignore and include sections
        $lines = explode("\n", $yamlContent);
        $hasPackages = false;
        $inIgnore = false;
        $inInclude = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            $originalLine = $line;

            // Skip empty lines and pure comment lines
            if (empty($trimmedLine) || strpos($trimmedLine, '#') === 0) {
                continue;
            }

            // Check for section headers
            if (preg_match('/^ignore:\s*$/', $trimmedLine)) {
                $inIgnore = true;
                $inInclude = false;
                continue;
            }
            if (preg_match('/^include:\s*$/', $trimmedLine)) {
                $inInclude = true;
                $inIgnore = false;
                continue;
            }

            // If we find a line starting with "- " (package entry), it has content
            if (($inIgnore || $inInclude) && preg_match('/^\s*-\s+([^#]+)/', $originalLine, $matches)) {
                $package = trim($matches[1]);
                if (!empty($package)) {
                    $hasPackages = true;
                    break;
                }
            }

            // End of section: new top-level key
            if (($inIgnore || $inInclude) && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*:\s*$/', $trimmedLine)) {
                $inIgnore = false;
                $inInclude = false;
            }
        }

        // If no packages found, it's safe to migrate (it's just template/comments)
        return !$hasPackages;
    }

    /**
     * Update .gitignore on update (without regenerating files).
     *
     * @param IOInterface $io The IO interface
     */
    private function updateGitignoreOnUpdate(IOInterface $io): void
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        $projectDir = dirname((string) $vendorDir);
        $this->updateGitignore($projectDir, $io);
    }

    /**
     * Migrate old TXT configuration file to YAML format.
     *
     * @param string      $txtPath  Path to the old TXT file
     * @param string      $yamlPath Path to the new YAML file
     * @param IOInterface $io       The IO interface
     */
    private function migrateTxtToYaml(string $txtPath, string $yamlPath, IOInterface $io): void
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

        // If YAML already exists, merge instead of overwriting
        if (file_exists($yamlPath)) {
            $yamlContent = file_get_contents($yamlPath);
            $existingIgnorePackages = $this->extractPackagesFromYamlIgnoreSection($yamlContent);

            // Merge packages (avoid duplicates)
            $allPackages = array_unique(array_merge($existingIgnorePackages, $packages));
            sort($allPackages);

            // Rebuild YAML preserving structure and include section
            $yamlLines = explode("\n", $yamlContent);
            $newYamlLines = [];
            $inIgnore = false;
            $ignoreSectionProcessed = false;
            $inInclude = false;

            foreach ($yamlLines as $line) {
                $trimmedLine = trim($line);

                // Detect section headers
                if (preg_match('/^ignore:\s*$/', $trimmedLine)) {
                    $inIgnore = true;
                    $inInclude = false;
                    $newYamlLines[] = $line;
                    // Insert merged packages
                    if (!$ignoreSectionProcessed) {
                        foreach ($allPackages as $pkg) {
                            $newYamlLines[] = "  - {$pkg}";
                        }
                        $ignoreSectionProcessed = true;
                    }
                    continue;
                }
                if (preg_match('/^include:\s*$/', $trimmedLine)) {
                    $inInclude = true;
                    $inIgnore = false;
                    $newYamlLines[] = $line;
                    continue;
                }

                // Skip old ignore entries (we've already added merged ones)
                if ($inIgnore && !$ignoreSectionProcessed && preg_match('/^\s*-\s+([^#]+)/', $line)) {
                    continue; // Skip old ignore entries
                }

                // Keep everything else (comments, include section, etc.)
                $newYamlLines[] = $line;

                // Detect end of section
                if (($inIgnore || $inInclude) && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*:\s*$/', $trimmedLine)) {
                    $inIgnore = false;
                    $inInclude = false;
                }
            }

            // If ignore section wasn't found, add it
            if (!$ignoreSectionProcessed) {
                // Find a good place to insert (before include or at the end)
                $insertPos = count($newYamlLines);
                for ($i = 0; $i < count($newYamlLines); $i++) {
                    if (preg_match('/^include:\s*$/', trim($newYamlLines[$i]))) {
                        $insertPos = $i;
                        break;
                    }
                }
                array_splice($newYamlLines, $insertPos, 0, [
                    '# List of packages to ignore during update',
                    '# Ignored packages will still be displayed in the output with their available versions,',
                    "# but won't be included in the composer require commands.",
                    'ignore:',
                ]);
                foreach ($allPackages as $pkg) {
                    array_splice($newYamlLines, $insertPos + 4, 0, "  - {$pkg}");
                }
            }

            $yamlContent = implode("\n", $newYamlLines);
        } else {
            // Create new YAML content
            $yamlContent = "# Composer Update Helper Configuration\n";
            $yamlContent .= "# Configuration file for ignored and included packages during composer update suggestions\n";
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

            $yamlContent .= "\n# List of packages to force include during update\n";
            $yamlContent .= "# Included packages will be added to the composer require commands even if they are in the ignore list.\n";
            $yamlContent .= "# The include section has priority over the ignore section.\n";
            $yamlContent .= "include:\n";
            $yamlContent .= "  # Add packages to force include (uncomment and add more as needed)\n";
            $yamlContent .= "  # - some/package\n";
            $yamlContent .= "  # - another/package\n";
        }

        file_put_contents($yamlPath, $yamlContent);
        $io->write(sprintf('<info>Configuration migrated to %s</info>', basename($yamlPath)));
    }

    /**
     * Extract packages from ignore section of YAML content.
     *
     * @param string $yamlContent The YAML content
     *
     * @return array<string> Array of package names
     */
    private function extractPackagesFromYamlIgnoreSection(string $yamlContent): array
    {
        $packages = [];
        $lines = explode("\n", $yamlContent);
        $inIgnore = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            // Check for section headers
            if (preg_match('/^ignore:\s*$/', $trimmedLine)) {
                $inIgnore = true;
                continue;
            }
            if (preg_match('/^include:\s*$/', $trimmedLine)) {
                $inIgnore = false;
                continue;
            }

            // Extract packages from ignore section only
            if ($inIgnore && preg_match('/^\s*-\s+([^#]+)/', $line, $matches)) {
                $package = trim($matches[1]);
                if (!empty($package)) {
                    $packages[] = $package;
                }
            }

            // End of section: new top-level key
            if ($inIgnore && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*:\s*$/', $trimmedLine)) {
                $inIgnore = false;
            }
        }

        return $packages;
    }

    /**
     * Update .gitignore to remove old TXT entry (if exists).
     * Note: .sh and .yaml files should NOT be in .gitignore as they should be committed to the repository.
     *
     * @param string      $projectDir The project root directory
     * @param IOInterface $io         The IO interface
     */
    private function updateGitignore(string $projectDir, IOInterface $io): void
    {
        $gitignorePath = $projectDir . '/.gitignore';

        // Only remove old TXT entry if it exists (for migration cleanup)
        // Do NOT add .sh or .yaml to .gitignore - these files should be in the repository
        $entriesToRemove = [
            'generate-composer-require.ignore.txt',
        ];

        // Also remove .sh and .yaml if they were previously added (cleanup)
        $entriesToRemoveAlso = [
            'generate-composer-require.sh',
            'generate-composer-require.yaml',
        ];

        if (!file_exists($gitignorePath)) {
            return; // No .gitignore file, nothing to do
        }

        $content = file_get_contents($gitignorePath);
        $lines = explode("\n", $content);
        $existingEntries = array_map('trim', $lines);
        $updated = false;

        // Remove old TXT entry if it exists
        foreach ($entriesToRemove as $entry) {
            $key = array_search($entry, $existingEntries, true);
            if ($key !== false) {
                unset($lines[$key]);
                $existingEntries = array_map('trim', $lines);
                $updated = true;
            }
        }

        // Remove .sh and .yaml entries if they exist (they shouldn't be ignored)
        foreach ($entriesToRemoveAlso as $entry) {
            $key = array_search($entry, $existingEntries, true);
            if ($key !== false) {
                unset($lines[$key]);
                $existingEntries = array_map('trim', $lines);
                $updated = true;
            }
        }

        if ($updated) {
            file_put_contents($gitignorePath, implode("\n", $lines) . "\n");
            $io->write('<info>Updated .gitignore to remove old Composer Update Helper entries</info>');
        }
    }

    /**
     * Remove files from the project root.
     *
     * @param IOInterface $io The IO interface
     */
    private function removeFiles(IOInterface $io): void
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        $projectDir = dirname((string) $vendorDir);

        $files = [
            'generate-composer-require.sh',
            // Note: We don't remove the config file as it may contain user configuration
        ];

        foreach ($files as $file) {
            $path = $projectDir . '/' . $file;

            if (file_exists($path)) {
                $io->write(sprintf('<info>Removing %s</info>', $file));
                unlink($path);
            }
        }
    }
}
