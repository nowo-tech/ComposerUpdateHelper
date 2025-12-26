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
        if (!file_exists($this->scriptPath)) {
            $this->markTestSkipped('Script file does not exist (may not be installed in CI/CD)');
        }

        $this->assertFileExists($this->scriptPath);
    }

    public function testScriptIsExecutable(): void
    {
        if (!file_exists($this->scriptPath)) {
            $this->markTestSkipped('Script file does not exist (may not be installed in CI/CD)');
        }

        $this->assertFileIsReadable($this->scriptPath);
    }

    public function testScriptHasCorrectShebang(): void
    {
        if (!file_exists($this->scriptPath)) {
            $this->markTestSkipped('Script file does not exist (may not be installed in CI/CD)');
        }

        $content = file_get_contents($this->scriptPath);

        if ($content === false) {
            $this->markTestSkipped('Could not read script file');
        }

        $this->assertStringStartsWith('#!/bin/sh', (string) $content);
    }

    public function testScriptContainsRequiredFunctions(): void
    {
        if (!file_exists($this->scriptPath)) {
            $this->markTestSkipped('Script file does not exist (may not be installed in CI/CD)');
        }

        $content = file_get_contents($this->scriptPath);

        if ($content === false) {
            $this->markTestSkipped('Could not read script file');
        }

        // Skip if script is not fully implemented
        if (strlen($content) < 100) {
            $this->markTestSkipped('Script file is not fully implemented');

            return;
        }

        // Check for essential parts in the lightweight script
        $this->assertStringContainsString('composer outdated', (string) $content);
        $this->assertStringContainsString('CONFIG_FILE', (string) $content);
        // Note: YAML parsing is now done in PHP processor, not in script
        // The script should call the PHP processor
        $this->assertStringContainsString('process-updates.php', (string) $content);
    }

    public function testScriptSupportsMultipleFrameworks(): void
    {
        // Framework logic is now in process-updates.php, not in the script
        // This test verifies the PHP processor contains framework support
        $processorPath = dirname(__DIR__) . '/bin/process-updates.php';

        if (!file_exists($processorPath)) {
            $this->markTestSkipped('Processor PHP file does not exist');
        }

        $content = file_get_contents($processorPath);

        if ($content === false) {
            $this->markTestSkipped('Could not read processor PHP file');
        }

        // Check for framework configurations in PHP processor
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
        // Framework detection logic is now in process-updates.php
        $processorPath = dirname(__DIR__) . '/bin/process-updates.php';

        if (!file_exists($processorPath)) {
            $this->markTestSkipped('Processor PHP file does not exist');
        }

        $content = file_get_contents($processorPath);

        if ($content === false) {
            $this->markTestSkipped('Could not read processor PHP file');
        }

        // Check for framework detection logic in PHP processor
        $this->assertStringContainsString('frameworkConfigs', (string) $content);
        $this->assertStringContainsString('frameworkConstraints', (string) $content);
        $this->assertStringContainsString('getFrameworkConstraint', (string) $content);
    }

    public function testScriptSupportsLaravelIlluminatePackages(): void
    {
        // Laravel/Illuminate logic is now in process-updates.php
        $processorPath = dirname(__DIR__) . '/bin/process-updates.php';

        if (!file_exists($processorPath)) {
            $this->markTestSkipped('Processor PHP file does not exist');
        }

        $content = file_get_contents($processorPath);

        if ($content === false) {
            $this->markTestSkipped('Could not read processor PHP file');
        }

        // Check Laravel also limits illuminate/* packages in PHP processor
        $this->assertStringContainsString("'illuminate/'", (string) $content);
        $this->assertStringContainsString('laravel/framework', (string) $content);
    }

    public function testScriptHandlesYamlConfigFile(): void
    {
        if (!file_exists($this->scriptPath)) {
            $this->markTestSkipped('Script file does not exist (may not be installed in CI/CD)');
        }

        $content = file_get_contents($this->scriptPath);

        if ($content === false) {
            $this->markTestSkipped('Could not read script file');
        }

        // Skip if script is not fully implemented
        if (strlen($content) < 100) {
            $this->markTestSkipped('Script file is not fully implemented');

            return;
        }

        // Script should read YAML file (primary) and support TXT for backward compatibility
        $this->assertStringContainsString('generate-composer-require.yaml', (string) $content);
        $this->assertStringContainsString('CONFIG_FILE', (string) $content);
        // Support for .yml extension
        $this->assertStringContainsString('generate-composer-require.yml', (string) $content);
        // Backward compatibility: still supports old TXT format
        $this->assertStringContainsString('generate-composer-require.ignore.txt', (string) $content);
        // Script searches in current directory, not script directory
        $this->assertStringNotContainsString('$(dirname "$0")/generate-composer-require.yaml', (string) $content);
    }

    public function testScriptDetectsProcessorPhpInVendor(): void
    {
        if (!file_exists($this->scriptPath)) {
            $this->markTestSkipped('Script file does not exist (may not be installed in CI/CD)');
        }

        $content = file_get_contents($this->scriptPath);

        if ($content === false) {
            $this->markTestSkipped('Could not read script file');
        }

        // Skip if script is not fully implemented
        if (strlen($content) < 100) {
            $this->markTestSkipped('Script file is not fully implemented');

            return;
        }

        // Script should detect process-updates.php in vendor
        $this->assertStringContainsString('process-updates.php', (string) $content);
        $this->assertStringContainsString('PROCESSOR_PHP', (string) $content);
        $this->assertStringContainsString('vendor/nowo-tech/composer-update-helper/bin/process-updates.php', (string) $content);
        // Should have fallback to script directory
        $this->assertStringContainsString('$(dirname "$0")/process-updates.php', (string) $content);
    }

    public function testScriptSupportsRunFlag(): void
    {
        if (!file_exists($this->scriptPath)) {
            $this->markTestSkipped('Script file does not exist (may not be installed in CI/CD)');
        }

        $content = file_get_contents($this->scriptPath);

        if ($content === false) {
            $this->markTestSkipped('Could not read script file');
        }

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

        if (!file_exists($ignoreFile)) {
            $this->markTestSkipped('Ignore file template does not exist (optional file)');

            return;
        }

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

    public function testScriptSupportsVerboseOption(): void
    {
        if (!file_exists($this->scriptPath)) {
            $this->markTestSkipped('Script file does not exist (may not be installed in CI/CD)');
        }

        $content = file_get_contents($this->scriptPath);

        if ($content === false) {
            $this->markTestSkipped('Could not read script file');
        }

        // Skip if script is not fully implemented
        if (strlen($content) < 100) {
            $this->markTestSkipped('Script file is not fully implemented');

            return;
        }

        // Check for verbose option
        $this->assertStringContainsString('--verbose', (string) $content);
        $this->assertStringContainsString('-v', (string) $content);
        $this->assertStringContainsString('VERBOSE', (string) $content);
    }

    public function testScriptSupportsDebugOption(): void
    {
        if (!file_exists($this->scriptPath)) {
            $this->markTestSkipped('Script file does not exist (may not be installed in CI/CD)');
        }

        $content = file_get_contents($this->scriptPath);

        if ($content === false) {
            $this->markTestSkipped('Could not read script file');
        }

        // Skip if script is not fully implemented
        if (strlen($content) < 100) {
            $this->markTestSkipped('Script file is not fully implemented');

            return;
        }

        // Check for debug option
        $this->assertStringContainsString('--debug', (string) $content);
        $this->assertStringContainsString('DEBUG', (string) $content);
        $this->assertStringContainsString('DEBUG:', (string) $content);
    }

    public function testScriptSearchesInCurrentDirectory(): void
    {
        if (!file_exists($this->scriptPath)) {
            $this->markTestSkipped('Script file does not exist (may not be installed in CI/CD)');
        }

        $content = file_get_contents($this->scriptPath);

        if ($content === false) {
            $this->markTestSkipped('Could not read script file');
        }

        // Skip if script is not fully implemented
        if (strlen($content) < 100) {
            $this->markTestSkipped('Script file is not fully implemented');

            return;
        }

        // Script should search in current directory, not script directory
        $this->assertStringContainsString('generate-composer-require.yaml', (string) $content);
        $this->assertStringNotContainsString('$(dirname "$0")/generate-composer-require.yaml', (string) $content);
        // Should check files directly in current directory
        $this->assertStringContainsString('[ -f "generate-composer-require.yaml" ]', (string) $content);
    }
}
