<?php

declare(strict_types=1);

/**
 * Utility Functions
 * General utility functions for the update helper.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */
class Utils
{
    public const DEBUG_PREFIX = 'DEBUG: ';

    private const COMPOSER_REQUIRE = 'composer require';

    private const COMPOSER_REQUIRE_DEV = 'composer require --dev';

    private const COMPOSER_REQUIRE_FLAGS = '--with-all-dependencies';

    /**
     * Log debug message.
     */
    public static function debugLog(string $message, bool $debug = false): void
    {
        if ($debug) {
            error_log(self::DEBUG_PREFIX . $message);
        }
    }

    /**
     * Show progress message (only once per message type when $shown is provided, and only if not in debug mode).
     *
     * Deduplication state is caller-owned (no mutable statics — FrankenPHP worker / scenario B safe).
     *
     * @param string $messageType Unique identifier for this message type
     * @param string $message Message to show
     * @param bool $debug If true, don't show progress message (debug shows detailed info)
     * @param bool $verbose If true, show the message
     * @param array<string, true>|null $shown Optional per-run map of already shown message types
     */
    public static function showProgressMessage(string $messageType, string $message, bool $debug = false, bool $verbose = false, ?array &$shown = null): void
    {
        // Don't show progress in debug mode (debug already shows detailed info)
        if ($debug) {
            return;
        }

        // $verbose kept for call-site BC; progress is always emitted when not debug
        // (historical behaviour). Dedup uses caller-owned $shown — no mutable statics.

        if ($shown !== null) {
            if (isset($shown[$messageType])) {
                return;
            }
            $shown[$messageType] = true;
        }

        // Show the message (use error_log to stderr to not interfere with output)
        error_log($message);
    }

    /**
     * Normalize version (remove 'v' prefix).
     */
    public static function normalizeVersion(?string $version): ?string
    {
        if ($version === null) {
            return null;
        }

        return ltrim($version, 'v');
    }

    /**
     * Format package list output.
     */
    public static function formatPackageList(array $packages, string $label, string $indent = '     '): array
    {
        $output = [];
        foreach ($packages as $pkg) {
            $output[] = $indent . '- ' . $pkg . ' ' . $label;
        }

        return $output;
    }

    /**
     * Add packages to prod or dev arrays.
     */
    public static function addPackageToArray(string $name, string $constraint, array $devSet, array &$prod, array &$dev, bool $debug = false): void
    {
        $packageString = $name . ':' . $constraint;
        if (isset($devSet[$name])) {
            $dev[] = $packageString;
            self::debugLog("  - Action: ADDED to dev dependencies: {$packageString}", $debug);
        } else {
            $prod[] = $packageString;
            self::debugLog("  - Action: ADDED to prod dependencies: {$packageString}", $debug);
        }
    }

    /**
     * Build composer require command.
     */
    public static function buildComposerCommand(array $packages, bool $isDev = false): ?string
    {
        if (empty($packages)) {
            return null;
        }

        $baseCommand = $isDev ? self::COMPOSER_REQUIRE_DEV : self::COMPOSER_REQUIRE;

        return $baseCommand . ' ' . self::COMPOSER_REQUIRE_FLAGS . ' ' . implode(' ', $packages);
    }
}
