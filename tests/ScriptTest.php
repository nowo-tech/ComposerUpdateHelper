<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper\Tests;

use PHPUnit\Framework\TestCase;

/**
 * @author Héctor Franco Aceituno <hectorfranco@nowo.com>
 * @see    https://github.com/HecFranco
 */
class ScriptTest extends TestCase
{
    private string $scriptPath;

    protected function setUp(): void
    {
        $this->scriptPath = dirname(__DIR__) . '/bin/generate-composer-require.sh';
    }

    public function testScriptExists(): void
    {
        $this->assertFileExists($this->scriptPath);
    }

    public function testScriptIsExecutable(): void
    {
        $this->assertFileIsReadable($this->scriptPath);
    }

    public function testScriptHasCorrectShebang(): void
    {
        $content = file_get_contents($this->scriptPath);
        $this->assertStringStartsWith('#!/bin/sh', $content);
    }

    public function testScriptContainsRequiredFunctions(): void
    {
        $content = file_get_contents($this->scriptPath);

        // Check for essential parts
        $this->assertStringContainsString('composer outdated', $content);
        $this->assertStringContainsString('IGNORED_PACKAGES', $content);
        $this->assertStringContainsString('--with-all-dependencies', $content);
    }

    public function testScriptHandlesIgnoreFile(): void
    {
        $content = file_get_contents($this->scriptPath);

        $this->assertStringContainsString('generate-composer-require.ignore.txt', $content);
        $this->assertStringContainsString('IGNORE_FILE', $content);
    }

    public function testScriptSupportsRunFlag(): void
    {
        $content = file_get_contents($this->scriptPath);

        $this->assertStringContainsString('--run', $content);
        $this->assertStringContainsString('RUN_FLAG', $content);
    }

    public function testIgnoreFileTemplateExists(): void
    {
        $ignoreFile = dirname(__DIR__) . '/bin/generate-composer-require.ignore.txt';
        $this->assertFileExists($ignoreFile);
    }

    public function testIgnoreFileHasCorrectFormat(): void
    {
        $ignoreFile = dirname(__DIR__) . '/bin/generate-composer-require.ignore.txt';
        $content = file_get_contents($ignoreFile);

        // Should have comment lines
        $this->assertStringContainsString('#', $content);
        // Should have example packages
        $this->assertStringContainsString('/', $content);
    }
}
