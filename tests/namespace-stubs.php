<?php

declare(strict_types=1);

/**
 * Namespace-level hook for tests that need to simulate a post-migration YAML
 * mismatch (Installer::install / Plugin::handleConfigMigration verification).
 *
 * When $GLOBALS['__cuh_yaml_verify_target'] is set to the project YAML path,
 * the Nth read of that file can return altered content (see verify mode below).
 */

namespace NowoTech\ComposerUpdateHelper;

use function is_string;

function file_get_contents(string $filename, bool $use_include_path = false, $context = null, int $offset = 0, ?int $length = null): string|false
{
    $real = \file_get_contents($filename, $use_include_path, $context, $offset, $length);
    if ($real === false) {
        return false;
    }

    $target = $GLOBALS['__cuh_yaml_verify_target'] ?? null;
    if (!is_string($target) || $target === '') {
        return $real;
    }

    if ($filename !== $target) {
        return $real;
    }

    $mode                                   = $GLOBALS['__cuh_verify_mode'] ?? '';
    $key                                    = $filename;
    $c                                      = (int) ($GLOBALS['__cuh_yaml_read_count'][$key] ?? 0);
    $GLOBALS['__cuh_yaml_read_count'][$key] = $c + 1;

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
