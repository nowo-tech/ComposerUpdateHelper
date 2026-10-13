<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper;

use Composer\Script\Event;

/**
 * Static entry points for the Composer `scripts` section.
 *
 * Kept for backward compatibility only: both methods delegate to {@see Plugin}, which Composer already runs
 * automatically on `post-install-cmd` / `post-update-cmd` and on uninstall. They follow exactly the same rules
 * (a locally modified `generate-composer-require.sh` is only overwritten when
 * `extra.composer-update-helper.auto_update_wrapper` is true).
 *
 * @deprecated since 2.1, to be removed in 3.0. Remove `NowoTech\ComposerUpdateHelper\Installer::install` /
 *             `::uninstall` from your composer.json `scripts`; the Composer plugin does the same work.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 *
 * @see    https://github.com/HecFranco
 */
final class Installer
{
    /**
     * Install or update the wrapper script, YAML config and .gitignore entries (same as the plugin on post-install).
     *
     * @param Event $event The script event
     */
    public static function install(Event $event): void
    {
        self::createPlugin($event)->onPostInstall($event);
    }

    /**
     * Remove the wrapper script from the project root (same as the plugin on uninstall).
     *
     * @param Event $event The script event
     */
    public static function uninstall(Event $event): void
    {
        self::createPlugin($event)->uninstall($event->getComposer(), $event->getIO());
    }

    private static function createPlugin(Event $event): Plugin
    {
        $plugin = new Plugin();
        $plugin->activate($event->getComposer(), $event->getIO());

        return $plugin;
    }
}
