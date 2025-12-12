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
        $this->installFiles($event->getIO());
    }

    /**
     * Handle post-update command event.
     *
     * @param Event $event The script event
     */
    public function onPostUpdate(Event $event): void
    {
        $this->installFiles($event->getIO());
    }

    /**
     * Install files to the project root.
     *
     * @param IOInterface $io The IO interface
     */
    private function installFiles(IOInterface $io): void
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
