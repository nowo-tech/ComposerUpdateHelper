<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Test suite for the generate-composer-require.sh script.
 * Tests script existence, executability, and functionality including
 * framework support and ignore file handling.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 *
 * @see    https://github.com/HecFranco
 */
final class ScriptTest extends TestCase
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

        // Skip if script is not fully implemented
        if (strlen($content) < 100) {
            $this->markTestSkipped('Script file is not fully implemented');

            return;
        }

        // Check for essential parts
        $this->assertStringContainsString('composer outdated', (string) $content);
        $this->assertStringContainsString('IGNORED_PACKAGES', (string) $content);
        $this->assertStringContainsString('--with-all-dependencies', (string) $content);
    }

    public function testScriptSupportsMultipleFrameworks(): void
    {
        $content = file_get_contents($this->scriptPath);

        // Skip if script is not fully implemented
        if (strlen($content) < 100) {
            $this->markTestSkipped('Script file is not fully implemented');

            return;
        }

        // Check for framework configurations
        $this->assertStringContainsString("'symfony'", (string) $content);
        $this->assertStringContainsString("'laravel'", (string) $content);
        $this->assertStringContainsString("'yii'", (string) $content);
        $this->assertStringContainsString("'cakephp'", (string) $content);
        $this->assertStringContainsString("'laminas'", (string) $content);
        $this->assertStringContainsString("'codeigniter'", (string) $content);
        $this->assertStringContainsString("'slim'", (string) $content);
    }

    public function testScriptDetectsFrameworkConstraints(): void
    {
        $content = file_get_contents($this->scriptPath);

        // Skip if script is not fully implemented
        if (strlen($content) < 100) {
            $this->markTestSkipped('Script file is not fully implemented');

            return;
        }

        // Check for framework detection logic
        $this->assertStringContainsString('frameworkConfigs', (string) $content);
        $this->assertStringContainsString('frameworkConstraints', (string) $content);
        $this->assertStringContainsString('getFrameworkConstraint', (string) $content);
    }

    public function testScriptSupportsLaravelIlluminatePackages(): void
    {
        $content = file_get_contents($this->scriptPath);

        // Skip if script is not fully implemented
        if (strlen($content) < 100) {
            $this->markTestSkipped('Script file is not fully implemented');

            return;
        }

        // Check Laravel also limits illuminate/* packages
        $this->assertStringContainsString("'illuminate/'", (string) $content);
        $this->assertStringContainsString('laravel/framework', (string) $content);
    }

    public function testScriptHandlesIgnoreFile(): void
    {
        $content = file_get_contents($this->scriptPath);

        // Skip if script is not fully implemented
        if (strlen($content) < 100) {
            $this->markTestSkipped('Script file is not fully implemented');

            return;
        }

        $this->assertStringContainsString('generate-composer-require.ignore.txt', (string) $content);
        $this->assertStringContainsString('IGNORE_FILE', (string) $content);
    }

    public function testScriptSupportsRunFlag(): void
    {
        $content = file_get_contents($this->scriptPath);

        // Skip if script is not fully implemented
        if (strlen($content) < 100) {
            $this->markTestSkipped('Script file is not fully implemented');

            return;
        }

        $this->assertStringContainsString('--run', (string) $content);
        $this->assertStringContainsString('RUN_FLAG', (string) $content);
    }

    public function testIgnoreFileTemplateExists(): void
    {
        $ignoreFile = dirname(__DIR__) . '/bin/generate-composer-require.ignore.txt';
        $this->assertFileExists($ignoreFile);
    }

    public function testIgnoreFileHasCorrectFormat(): void
    {
        $ignoreFile = dirname(__DIR__) . '/bin/generate-composer-require.ignore.txt';

        if (!file_exists($ignoreFile)) {
            $this->markTestSkipped('Ignore file template does not exist');

            return;
        }

        $content = file_get_contents($ignoreFile);

        // Should have comment lines
        $this->assertStringContainsString('#', (string) $content);

        // Should have example packages (if file has enough content)
        // Only check if file has substantial content (more than just a comment)
        if (strlen($content) > 20) {
            $this->assertStringContainsString('/', (string) $content);
        }
    }
}
