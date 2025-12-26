<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper\Tests;

use Composer\{Composer, Config};
use Composer\IO\IOInterface;
use Composer\Script\{Event, ScriptEvents};
use NowoTech\ComposerUpdateHelper\Plugin;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for the Plugin class.
 * Tests the Composer plugin functionality including file installation,
 * event subscription, and .gitignore updates.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 *
 * @see    https://github.com/HecFranco
 */
final class PluginTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $events = Plugin::getSubscribedEvents();

        $this->assertIsArray($events);
        $this->assertArrayHasKey(ScriptEvents::POST_INSTALL_CMD, $events);
        $this->assertArrayHasKey(ScriptEvents::POST_UPDATE_CMD, $events);
        $this->assertEquals('onPostInstall', $events[ScriptEvents::POST_INSTALL_CMD]);
        $this->assertEquals('onPostUpdate', $events[ScriptEvents::POST_UPDATE_CMD]);
    }

    public function testActivateStoresComposerAndIo(): void
    {
        $plugin = new Plugin();
        $composer = $this->createMock(Composer::class);
        $io = $this->createMock(IOInterface::class);

        // Should not throw any exception
        $plugin->activate($composer, $io);

        $this->assertTrue(true);
    }

    public function testDeactivateDoesNothing(): void
    {
        $plugin = new Plugin();
        $composer = $this->createMock(Composer::class);
        $io = $this->createMock(IOInterface::class);

        // Should not throw any exception
        $plugin->deactivate($composer, $io);

        $this->assertTrue(true);
    }

    public function testUninstallRemovesFiles(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        mkdir($vendorDir, 0777, true);

        // Create test file
        file_put_contents($tempDir . '/generate-composer-require.sh', '#!/bin/sh');

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->uninstall($composer, $io);

        $this->assertFileDoesNotExist($tempDir . '/generate-composer-require.sh');

        // Cleanup
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testOnPostInstallInstallsFiles(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        // Create source file in temporary package directory (not in real project)
        $sourceFile = $binDir . '/generate-composer-require.sh';
        file_put_contents($sourceFile, '#!/bin/sh\necho "test"');

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);
        $io->expects($this->atLeastOnce())
            ->method('write')
            ->with($this->logicalOr(
                $this->stringContains('Installing'),
                $this->stringContains('Creating generate-composer-require.yaml'),
                $this->stringContains('Updated .gitignore')
            ));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostInstall($event);

        $this->assertFileExists($tempDir . '/generate-composer-require.sh');

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/generate-composer-require.yaml');
        @unlink($sourceFile);
        @rmdir($binDir);
        @rmdir($packageDir);
        @rmdir($vendorDir . '/nowo-tech');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testOnPostUpdateUpdatesGitignore(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        mkdir($vendorDir, 0777, true);

        // Create .gitignore file
        $gitignorePath = $tempDir . '/.gitignore';
        file_put_contents($gitignorePath, 'vendor/');

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);
        $io->expects($this->atLeastOnce())
            ->method('write')
            ->with($this->stringContains('Updated .gitignore'));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostUpdate($event);

        // Verify .gitignore was updated
        $gitignoreContent = file_get_contents($gitignorePath);
        $this->assertStringContainsString('generate-composer-require.sh', $gitignoreContent);
        $this->assertStringContainsString('generate-composer-require.yaml', $gitignoreContent);

        // Cleanup
        @unlink($gitignorePath);
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testInstallFilesSkipsWhenFileExists(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = __DIR__ . '/..';
        mkdir($vendorDir, 0777, true);

        // Create existing file with different content
        $existingContent = '#!/bin/sh\necho "old"';
        file_put_contents($tempDir . '/generate-composer-require.sh', $existingContent);

        // Create source file in package with new content
        $binDir = $packageDir . '/bin';

        if (!is_dir($binDir)) {
            mkdir($binDir, 0777, true);
        }

        $sourceFile = $binDir . '/generate-composer-require.sh';

        // Backup original file if it exists
        $originalContent = null;

        if (file_exists($sourceFile)) {
            $originalContent = file_get_contents($sourceFile);
        }

        try {
            file_put_contents($sourceFile, '#!/bin/sh\necho "new"');

            $config = $this->createMock(Config::class);
            $config->method('get')
                ->with('vendor-dir')
                ->willReturn($vendorDir);

            $composer = $this->createMock(Composer::class);
            $composer->method('getConfig')
                ->willReturn($config);

            $io = $this->createMock(IOInterface::class);
            $io->expects($this->atLeastOnce())
                ->method('write')
                ->with($this->logicalOr(
                    $this->stringContains('Creating generate-composer-require.yaml'),
                    $this->stringContains('Updated .gitignore')
                ));

            $event = $this->createMock(Event::class);
            $event->method('getIO')
                ->willReturn($io);

            $plugin = new Plugin();
            $plugin->activate($composer, $io);
            $plugin->onPostInstall($event);

            // File should still exist with original content (not updated)
            $this->assertFileExists($tempDir . '/generate-composer-require.sh');
            $this->assertStringContainsString('old', (string) file_get_contents($tempDir . '/generate-composer-require.sh'));
        } finally {
            // Restore original file
            if ($originalContent !== null) {
                file_put_contents($sourceFile, $originalContent);
            } elseif (file_exists($sourceFile)) {
                @unlink($sourceFile);
            }
        }

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/generate-composer-require.yaml');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testInstallFilesSkipsWhenContentMatches(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        $content = '#!/bin/sh\necho "same"';
        file_put_contents($tempDir . '/generate-composer-require.sh', $content);

        // Create source file in temporary package directory (not in real project)
        $sourceFile = $binDir . '/generate-composer-require.sh';
        file_put_contents($sourceFile, $content);

        // Create .gitignore with entries already present to avoid update message
        $gitignorePath = $tempDir . '/.gitignore';
        file_put_contents($gitignorePath, "# Composer Update Helper\ngenerate-composer-require.sh\ngenerate-composer-require.yaml\n");

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);
        // Since .gitignore already has the entries, no update message should be shown
        // and since file content matches, no installation/update messages should be shown
        $io->expects($this->never())
            ->method('write');

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostInstall($event);

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($sourceFile);
        @rmdir($binDir);
        @rmdir($packageDir);
        @rmdir($vendorDir . '/nowo-tech');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testInstallFilesHandlesMissingSourceFile(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        // Ensure source file doesn't exist (don't create it)
        $sourceFile = $binDir . '/generate-composer-require.sh';

        if (file_exists($sourceFile)) {
            @unlink($sourceFile);
        }

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);
        $io->expects($this->atLeastOnce())
            ->method('writeError')
            ->with($this->stringContains('Source file not found'));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostInstall($event);

        // Cleanup
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testInstallFilesCreatesYamlConfigFileIfNotExists(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        // Simulate package is in vendor (not development mode) to avoid using real project directory
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($vendorDir, 0777, true);
        mkdir($packageDir, 0777, true);

        // Create source files in package (using temporary directory, not real project)
        $binDir = $packageDir . '/bin';

        if (!is_dir($binDir)) {
            mkdir($binDir, 0777, true);
        }

        file_put_contents($binDir . '/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($binDir . '/generate-composer-require.yaml', '# YAML config file');

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);
        $io->expects($this->atLeastOnce())
            ->method('write')
            ->with($this->logicalOr(
                $this->stringContains('Installing'),
                $this->stringContains('Creating generate-composer-require.yaml'),
                $this->stringContains('Updated .gitignore')
            ));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostInstall($event);

        $this->assertFileExists($tempDir . '/generate-composer-require.yaml');

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/generate-composer-require.yaml');
        @unlink($binDir . '/generate-composer-require.sh');
        @unlink($binDir . '/generate-composer-require.yaml');
        @rmdir($binDir);
        @rmdir($packageDir);
        @rmdir($vendorDir . '/nowo-tech');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testInstallFilesUpdatesGitignore(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        // Create source file in temporary package directory (not in real project)
        $sourceFile = $binDir . '/generate-composer-require.sh';
        file_put_contents($sourceFile, '#!/bin/sh\necho "test"');

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);
        $io->expects($this->atLeastOnce())
            ->method('write')
            ->with($this->logicalOr(
                $this->stringContains('Installing'),
                $this->stringContains('Creating generate-composer-require.yaml'),
                $this->stringContains('Updated .gitignore')
            ));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostInstall($event);

        // Verify .gitignore was created/updated
        $gitignorePath = $tempDir . '/.gitignore';
        $this->assertFileExists($gitignorePath);

        $gitignoreContent = file_get_contents($gitignorePath);
        $this->assertStringContainsString('generate-composer-require.sh', $gitignoreContent);
        $this->assertStringContainsString('generate-composer-require.yaml', $gitignoreContent);
        $this->assertStringContainsString('# Composer Update Helper', $gitignoreContent);

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/.gitignore');
        @unlink($sourceFile);
        @rmdir($binDir);
        @rmdir($packageDir);
        @rmdir($vendorDir . '/nowo-tech');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testInstallFilesDoesNotDuplicateGitignoreEntries(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        // Create existing .gitignore with entries
        $gitignorePath = $tempDir . '/.gitignore';
        file_put_contents($gitignorePath, "# Existing entries\ngenerate-composer-require.sh\nvendor/\n");

        // Create source file in temporary package directory (not in real project)
        $sourceFile = $binDir . '/generate-composer-require.sh';
        file_put_contents($sourceFile, '#!/bin/sh\necho "test"');

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostInstall($event);

        // Verify .gitignore was updated but entries are not duplicated
        $gitignoreContent = file_get_contents($gitignorePath);
        $this->assertStringContainsString('generate-composer-require.sh', $gitignoreContent);
        $this->assertStringContainsString('generate-composer-require.yaml', $gitignoreContent);

        // Count occurrences - should be only one of each
        $this->assertEquals(1, substr_count($gitignoreContent, 'generate-composer-require.sh'));
        $this->assertEquals(1, substr_count($gitignoreContent, 'generate-composer-require.yaml'));

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/.gitignore');
        @unlink($sourceFile);
        @rmdir($binDir);
        @rmdir($packageDir);
        @rmdir($vendorDir . '/nowo-tech');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testInstallFilesForceUpdate(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = __DIR__ . '/..';
        mkdir($vendorDir, 0777, true);

        // Create existing file with old content
        file_put_contents($tempDir . '/generate-composer-require.sh', '#!/bin/sh\necho "old"');

        // Create source file in package with new content
        $binDir = $packageDir . '/bin';

        if (!is_dir($binDir)) {
            mkdir($binDir, 0777, true);
        }

        $sourceFile = $binDir . '/generate-composer-require.sh';

        // Backup original file if it exists
        $originalContent = null;

        if (file_exists($sourceFile)) {
            $originalContent = file_get_contents($sourceFile);
        }

        try {
            file_put_contents($sourceFile, '#!/bin/sh\necho "new"');

            $config = $this->createMock(Config::class);
            $config->method('get')
                ->with('vendor-dir')
                ->willReturn($vendorDir);

            $composer = $this->createMock(Composer::class);
            $composer->method('getConfig')
                ->willReturn($config);

            $io = $this->createMock(IOInterface::class);
            $io->expects($this->atLeastOnce())
                ->method('write')
                ->with($this->logicalOr(
                    $this->stringContains('Updating'),
                    $this->stringContains('Creating generate-composer-require.ignore.txt'),
                    $this->stringContains('Updated .gitignore')
                ));

            $plugin = new Plugin();
            $plugin->activate($composer, $io);

            // Use reflection to call private installFiles method with forceUpdate = true
            $reflection = new \ReflectionClass($plugin);
            $method = $reflection->getMethod('installFiles');
            $method->setAccessible(true);
            $method->invoke($plugin, $io, true);

            // File should be updated with new content
            $this->assertFileExists($tempDir . '/generate-composer-require.sh');
            $this->assertStringContainsString('new', (string) file_get_contents($tempDir . '/generate-composer-require.sh'));
        } finally {
            // Restore original file
            if ($originalContent !== null) {
                file_put_contents($sourceFile, $originalContent);
            } elseif (file_exists($sourceFile)) {
                @unlink($sourceFile);
            }
        }

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/generate-composer-require.yaml');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testMigratesTxtToYamlWhenTxtExists(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        // Create source files
        file_put_contents($binDir . '/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($binDir . '/generate-composer-require.yaml', '# YAML config');

        // Create old TXT file in project (simulating upgrade scenario)
        $oldTxtFile = $tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "doctrine/orm\nsymfony/security-bundle\n# Comment line\n");

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);
        $io->expects($this->atLeastOnce())
            ->method('write')
            ->with($this->logicalOr(
                $this->stringContains('Installing'),
                $this->stringContains('Migrating configuration from TXT to YAML format'),
                $this->stringContains('Configuration migrated to'),
                $this->stringContains('Removed old generate-composer-require.ignore.txt file'),
                $this->stringContains('Updated .gitignore')
            ));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostInstall($event);

        // Verify YAML file was created
        $yamlFile = $tempDir . '/generate-composer-require.yaml';
        $this->assertFileExists($yamlFile);

        // Verify content was migrated correctly
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('doctrine/orm', $yamlContent);
        $this->assertStringContainsString('symfony/security-bundle', $yamlContent);
        $this->assertStringContainsString('ignore:', $yamlContent);
        $this->assertStringContainsString('Migrated from generate-composer-require.ignore.txt', $yamlContent);

        // Verify old TXT file was deleted after migration
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/generate-composer-require.yaml');
        @unlink($oldTxtFile);
        @unlink($binDir . '/generate-composer-require.sh');
        @unlink($binDir . '/generate-composer-require.yaml');
        @rmdir($binDir);
        @rmdir($packageDir);
        @rmdir($vendorDir . '/nowo-tech');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testOnPostUpdateMigratesTxtToYaml(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        file_put_contents($binDir . '/generate-composer-require.yaml', '# YAML config');

        // Create old TXT file in project (simulating upgrade scenario)
        $oldTxtFile = $tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "doctrine/orm\n");

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);
        $io->expects($this->atLeastOnce())
            ->method('write')
            ->with($this->logicalOr(
                $this->stringContains('Migrating configuration from TXT to YAML format'),
                $this->stringContains('Configuration migrated to'),
                $this->stringContains('Removed old generate-composer-require.ignore.txt file'),
                $this->stringContains('Updated .gitignore')
            ));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostUpdate($event);

        // Verify YAML file was created
        $yamlFile = $tempDir . '/generate-composer-require.yaml';
        $this->assertFileExists($yamlFile);

        // Verify old TXT file was deleted after migration
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.yaml');
        @unlink($binDir . '/generate-composer-require.yaml');
        @rmdir($binDir);
        @rmdir($packageDir);
        @rmdir($vendorDir . '/nowo-tech');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }
}
