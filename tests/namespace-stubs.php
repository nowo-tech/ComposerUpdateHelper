<?php

declare(strict_types=1);

/**
 * Namespace-level hook for tests that need to simulate a post-migration YAML
 * mismatch (Installer::install / Plugin::handleConfigMigration verification).
 *
 * Hooks use putenv/getenv (not static props / $GLOBALS) so FrankenPHP worker
 * PHPStan rules stay clean under REQ-CS-005/006.
 */

namespace NowoTech\ComposerUpdateHelper;

use function getenv;
use function is_string;
use function putenv;

final class TestIoHooks
{
    private const ENV_FORCE_FGC_FALSE = 'CUH_TEST_FORCE_FGC_FALSE';
    private const ENV_YAML_TARGET = 'CUH_TEST_YAML_VERIFY_TARGET';
    private const ENV_VERIFY_MODE = 'CUH_TEST_VERIFY_MODE';
    private const ENV_YAML_READ_COUNT = 'CUH_TEST_YAML_READ_COUNT';

    public static function setForceFgcFalse(?string $path): void
    {
        if (null === $path || '' === $path) {
            putenv(self::ENV_FORCE_FGC_FALSE);
        } else {
            putenv(self::ENV_FORCE_FGC_FALSE.'='.$path);
        }
    }

    public static function forceFgcFalse(): ?string
    {
        $v = getenv(self::ENV_FORCE_FGC_FALSE);

        return is_string($v) && '' !== $v ? $v : null;
    }

    public static function setYamlVerify(string $target, string $mode): void
    {
        putenv(self::ENV_YAML_TARGET.'='.$target);
        putenv(self::ENV_VERIFY_MODE.'='.$mode);
        putenv(self::ENV_YAML_READ_COUNT.'='.json_encode([]));
    }

    public static function yamlVerifyTarget(): ?string
    {
        $v = getenv(self::ENV_YAML_TARGET);

        return is_string($v) && '' !== $v ? $v : null;
    }

    public static function verifyMode(): string
    {
        $v = getenv(self::ENV_VERIFY_MODE);

        return is_string($v) ? $v : '';
    }

    public static function bumpYamlReadCount(string $key): int
    {
        $raw = getenv(self::ENV_YAML_READ_COUNT);
        /** @var array<string, int> $counts */
        $counts = [];
        if (is_string($raw) && '' !== $raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                foreach ($decoded as $k => $n) {
                    if (is_string($k) && is_int($n)) {
                        $counts[$k] = $n;
                    } elseif (is_string($k) && is_numeric($n)) {
                        $counts[$k] = (int) $n;
                    }
                }
            }
        }
        $c = $counts[$key] ?? 0;
        $counts[$key] = $c + 1;
        putenv(self::ENV_YAML_READ_COUNT.'='.json_encode($counts));

        return $c;
    }

    public static function reset(): void
    {
        putenv(self::ENV_FORCE_FGC_FALSE);
        putenv(self::ENV_YAML_TARGET);
        putenv(self::ENV_VERIFY_MODE);
        putenv(self::ENV_YAML_READ_COUNT);
    }
}

function file_get_contents(string $filename, bool $use_include_path = false, $context = null, int $offset = 0, ?int $length = null): string|false
{
    $forceFalse = TestIoHooks::forceFgcFalse();
    if (is_string($forceFalse) && $forceFalse !== '' && $filename === $forceFalse) {
        return false;
    }

    $real = \file_get_contents($filename, $use_include_path, $context, $offset, $length);
    if ($real === false) {
        return false;
    }

    $target = TestIoHooks::yamlVerifyTarget();
    if (!is_string($target) || $target === '') {
        return $real;
    }

    if ($filename !== $target) {
        return $real;
    }

    $mode = TestIoHooks::verifyMode();
    $c = TestIoHooks::bumpYamlReadCount($filename);

    // installer: isYamlEmptyOrTemplate (0), migrate merge read (1), verification (2) -> corrupt on 3rd read
    if ($mode === 'installer' && $c === 2) {
        return (string) preg_replace('/^\s*-\s+pkg\/two\s*$/m', '', $real);
    }

    // plugin: migrate writes YAML with no prior project YAML reads; verification is 1st read of project YAML
    if ($mode === 'plugin' && $c === 0) {
        return (string) preg_replace('/^\s*-\s+pkg\/two\s*$/m', '', $real);
    }

    return $real;
}
