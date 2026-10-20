<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper;

use RuntimeException;

use function sprintf;

/**
 * Read a file as string or throw (avoids string|false edges for PHPStan).
 *
 * Uses unqualified {@see file_get_contents()} so the test namespace stub can intercept reads.
 */
final class SafeFileReader
{
    /**
     * Return the whole contents of a file.
     *
     * @param string $path Path of the file to read
     *
     * @throws RuntimeException When the file cannot be read
     */
    public static function read(string $path): string
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException(sprintf('Unable to read file "%s".', $path));
        }

        return $content;
    }
}
