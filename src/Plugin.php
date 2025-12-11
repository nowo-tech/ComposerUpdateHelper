<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

/**
 * Composer plugin that installs the generate-composer-require script.
 * Works with any PHP project (Symfony, Laravel, Yii, CodeIgniter, etc.)
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.com>
 * @see    https://github.com/HecFranco
 */
class Plugin implements PluginInterface, EventSubscriberInterface
{
    private Composer $composer;
    private IOInterface $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        $this->removeFiles($io);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'onPostInstall',
            ScriptEvents::POST_UPDATE_CMD => 'onPostUpdate',
        ];
    }

    public function onPostInstall(Event $event): void
    {
        $this->installFiles($event->getIO());
    }

    public function onPostUpdate(Event $event): void
    {
        $this->installFiles($event->getIO());
    }

    private function installFiles(IOInterface $io): void
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        $projectDir = dirname($vendorDir);
        $packageDir = __DIR__ . '/..';

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

            if (file_exists($destPath)) {
                // Check if it's the same content
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

        // Create ignore file only if it doesn't exist (don't overwrite user's config)
        $ignoreSource = $packageDir . '/bin/generate-composer-require.ignore.txt';
        $ignoreDest = $projectDir . '/generate-composer-require.ignore.txt';

        if (!file_exists($ignoreDest) && file_exists($ignoreSource)) {
            $io->write('<info>Creating generate-composer-require.ignore.txt</info>');
            copy($ignoreSource, $ignoreDest);
        }
    }

    private function removeFiles(IOInterface $io): void
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        $projectDir = dirname($vendorDir);

        $files = [
            'generate-composer-require.sh',
            // Note: We don't remove the ignore file as it may contain user configuration
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
