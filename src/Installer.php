<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper;

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
        // Use it when available, fallback to implicit (0755) for PHP 7.4 and 8.0
        if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
            return 0o755;
        }

        return 0755;
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

        // Create ignore file only if it doesn't exist
        $ignoreSource = $packageDir . '/bin/generate-composer-require.ignore.txt';
        $ignoreDest = $projectDir . '/generate-composer-require.ignore.txt';

        if (!file_exists($ignoreDest) && file_exists($ignoreSource)) {
            $io->write('<info>Creating generate-composer-require.ignore.txt</info>');
            copy($ignoreSource, $ignoreDest);
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
