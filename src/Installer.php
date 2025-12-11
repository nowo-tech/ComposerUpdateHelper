<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper;

use Composer\Script\Event;

/**
 * Static installer for composer scripts.
 * Can be used directly in composer.json scripts section.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.com>
 *
 * @see    https://github.com/HecFranco
 */
class Installer
{
    /**
     * Install files to the project root.
     *
     * @param Event $event The script event
     */
    public static function install(Event $event): void
    {
        $io = $event->getIO();
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $projectDir = dirname($vendorDir);
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
            chmod($destPath, 0755);
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
        $projectDir = dirname($vendorDir);

        $file = $projectDir . '/generate-composer-require.sh';
        if (file_exists($file)) {
            $io->write('<info>Removing generate-composer-require.sh</info>');
            unlink($file);
        }
    }
}
