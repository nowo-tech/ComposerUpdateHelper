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
        // Also create process-updates.php in vendor (should NOT be copied)
        file_put_contents($binDir . '/process-updates.php', '<?php echo "test";');

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
        // Verify process-updates.php is NOT copied (stays in vendor)
        $this->assertFileDoesNotExist($tempDir . '/process-updates.php');
        $this->assertFileExists($binDir . '/process-updates.php');

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/generate-composer-require.yaml');
        @unlink($sourceFile);
        @unlink($binDir . '/process-updates.php');
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
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        // Create source script in vendor
        file_put_contents($binDir . '/generate-composer-require.sh', '#!/bin/sh\necho "updated"');

        // Create .gitignore file with old entries that should be removed
        $gitignorePath = $tempDir . '/.gitignore';
        file_put_contents($gitignorePath, "vendor/\ngenerate-composer-require.sh\ngenerate-composer-require.yaml\n");

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
                $this->stringContains('Updated .gitignore'),
                $this->stringContains('Creating generate-composer-require.yaml'),
                $this->stringContains('Installing'),
                $this->stringContains('Updating')
            ));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostUpdate($event);

        // Verify script was installed/updated
        $this->assertFileExists($tempDir . '/generate-composer-require.sh');

        // Verify .gitignore was updated (should remove old entries, not add new ones)
        $gitignoreContent = file_get_contents($gitignorePath);
        // .sh and .yaml should NOT be in .gitignore (they should be committed)
        $this->assertStringNotContainsString('generate-composer-require.sh', $gitignoreContent);
        $this->assertStringNotContainsString('generate-composer-require.yaml', $gitignoreContent);
        // But vendor/ should still be there
        $this->assertStringContainsString('vendor/', $gitignoreContent);

        // Cleanup
        @unlink($gitignorePath);
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/generate-composer-require.yaml');
        @unlink($binDir . '/generate-composer-require.sh');
        @rmdir($binDir);
        @rmdir($packageDir);
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testInstallFilesSkipsWhenFileExists(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        // Create existing file with different content
        $existingContent = '#!/bin/sh\necho "old"';
        file_put_contents($tempDir . '/generate-composer-require.sh', $existingContent);

        // Create source file in package with new content
        $sourceFile = $binDir . '/generate-composer-require.sh';
        file_put_contents($sourceFile, '#!/bin/sh\necho "new"');

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);
        // When content differs, file should be updated
        // Allow any of these messages (at least one must appear)
        $io->expects($this->atLeastOnce())
            ->method('write')
            ->with($this->logicalOr(
                $this->stringContains('Updating generate-composer-require.sh'),
                $this->stringContains('Creating generate-composer-require.yaml'),
                $this->stringContains('Updated .gitignore'),
                $this->stringContains('updated .gitignore')
            ));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostInstall($event);

        // File should be updated with new content
        $this->assertFileExists($tempDir . '/generate-composer-require.sh');
        $this->assertStringContainsString('new', (string) file_get_contents($tempDir . '/generate-composer-require.sh'));

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

        // Create .gitignore without entries (they shouldn't be there anyway)
        $gitignorePath = $tempDir . '/.gitignore';
        file_put_contents($gitignorePath, "vendor/\n");

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);
        // Since file content matches and no .gitignore updates needed, no messages should be shown
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
        // process-updates.php should NOT be copied
        file_put_contents($binDir . '/process-updates.php', '<?php echo "processor";');

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
        // Verify process-updates.php is NOT copied (stays in vendor)
        $this->assertFileDoesNotExist($tempDir . '/process-updates.php');
        $this->assertFileExists($binDir . '/process-updates.php');

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/generate-composer-require.yaml');
        @unlink($binDir . '/generate-composer-require.sh');
        @unlink($binDir . '/generate-composer-require.yaml');
        @unlink($binDir . '/process-updates.php');
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

        // Verify .sh and .yaml are NOT in .gitignore (they should be committed to repo)
        $gitignorePath = $tempDir . '/.gitignore';
        if (file_exists($gitignorePath)) {
            $gitignoreContent = file_get_contents($gitignorePath);
            $this->assertStringNotContainsString('generate-composer-require.sh', $gitignoreContent);
            $this->assertStringNotContainsString('generate-composer-require.yaml', $gitignoreContent);
        }

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

        // Create existing .gitignore with old entries (should be removed)
        $gitignorePath = $tempDir . '/.gitignore';
        file_put_contents($gitignorePath, "# Existing entries\ngenerate-composer-require.sh\ngenerate-composer-require.yaml\nvendor/\n");

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

        // Verify .gitignore was updated - old entries should be removed
        $gitignoreContent = file_get_contents($gitignorePath);
        // .sh and .yaml should NOT be in .gitignore (they should be committed)
        $this->assertStringNotContainsString('generate-composer-require.sh', $gitignoreContent);
        $this->assertStringNotContainsString('generate-composer-require.yaml', $gitignoreContent);
        // But vendor/ should still be there
        $this->assertStringContainsString('vendor/', $gitignoreContent);

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
                    $this->stringContains('Creating generate-composer-require.yaml'),
                    $this->stringContains('Updated .gitignore')
                ));

            // Also create process-updates.php in vendor (should NOT be copied)
            file_put_contents($binDir . '/process-updates.php', '<?php echo "processor";');

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
            // Verify process-updates.php is NOT copied (stays in vendor)
            $this->assertFileDoesNotExist($tempDir . '/process-updates.php');
            $this->assertFileExists($binDir . '/process-updates.php');
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
        @unlink($binDir . '/process-updates.php');
        @rmdir($binDir);
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

    public function testMigratesTxtToYamlWhenYamlExistsButIsEmpty(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        file_put_contents($binDir . '/generate-composer-require.yaml', '# YAML config');

        // Create empty YAML file in project
        $yamlFile = $tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, '');

        // Create old TXT file with packages
        $oldTxtFile = $tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "doctrine/orm\nsymfony/security-bundle\n");

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

        // Verify YAML file was updated with migrated content
        $this->assertFileExists($yamlFile);
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('doctrine/orm', $yamlContent);
        $this->assertStringContainsString('symfony/security-bundle', $yamlContent);
        $this->assertStringContainsString('ignore:', $yamlContent);

        // Verify old TXT file was deleted
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

    public function testMigratesTxtToYamlWhenYamlExistsButIsTemplateOnly(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        file_put_contents($binDir . '/generate-composer-require.yaml', '# YAML config');

        // Create YAML file with only template (commented packages)
        $yamlFile = $tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "# Composer Update Helper Configuration\nignore:\n  # - doctrine/orm\n  # - symfony/security-bundle\n");

        // Create old TXT file with packages
        $oldTxtFile = $tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "doctrine/orm\nsymfony/security-bundle\n");

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

        // Verify YAML file was updated with migrated content
        $this->assertFileExists($yamlFile);
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('doctrine/orm', $yamlContent);
        $this->assertStringContainsString('symfony/security-bundle', $yamlContent);
        $this->assertStringContainsString('ignore:', $yamlContent);

        // Verify old TXT file was deleted
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

    public function testDoesNotMigrateTxtWhenYamlHasUserPackages(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        file_put_contents($binDir . '/generate-composer-require.yaml', '# YAML config');

        // Create YAML file with user-defined packages (not just template)
        $yamlFile = $tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "# Composer Update Helper Configuration\nignore:\n  - existing/package\n  - another/package\n");

        // Create old TXT file with different packages
        $oldTxtFile = $tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "doctrine/orm\nsymfony/security-bundle\n");

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);
        // Should NOT show migration messages
        $io->expects($this->never())
            ->method('write')
            ->with($this->stringContains('Migrating configuration from TXT to YAML format'));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostUpdate($event);

        // Verify YAML file was NOT changed (preserves user packages)
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('existing/package', $yamlContent);
        $this->assertStringContainsString('another/package', $yamlContent);
        $this->assertStringNotContainsString('doctrine/orm', $yamlContent);
        $this->assertStringNotContainsString('symfony/security-bundle', $yamlContent);

        // Verify old TXT file still exists (not migrated)
        $this->assertFileExists($oldTxtFile);

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.yaml');
        @unlink($oldTxtFile);
        @unlink($binDir . '/generate-composer-require.yaml');
        @rmdir($binDir);
        @rmdir($packageDir);
        @rmdir($vendorDir . '/nowo-tech');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testMigrationReadsIncludeSectionFromYaml(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        file_put_contents($binDir . '/generate-composer-require.yaml', '# YAML config');

        // Create YAML file with both ignore and include sections
        $yamlFile = $tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "ignore:\n  - package1/one\n  - package2/two\ninclude:\n  - included1/one\n  - included2/two\n");

        // Create old TXT file with packages
        $oldTxtFile = $tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "package1/one\npackage2/two\n");

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
                $this->stringContains('Removed old generate-composer-require.ignore.txt file')
            ));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostUpdate($event);

        // Verify YAML file was updated with migrated content
        $this->assertFileExists($yamlFile);
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('package1/one', $yamlContent);
        $this->assertStringContainsString('package2/two', $yamlContent);
        // Include section should still be there
        $this->assertStringContainsString('included1/one', $yamlContent);
        $this->assertStringContainsString('included2/two', $yamlContent);

        // Verify old TXT file was deleted
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

    public function testIsYamlEmptyOrTemplateDetectsIncludeSection(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        file_put_contents($binDir . '/generate-composer-require.yaml', '# YAML config');

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

        // Use reflection to call private isYamlEmptyOrTemplate method
        $reflection = new \ReflectionClass($plugin);
        $method = $reflection->getMethod('isYamlEmptyOrTemplate');
        $method->setAccessible(true);

        // Test with YAML that has only include section (should be considered empty for migration)
        $yamlFile = $tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "ignore:\n  # - package1\ninclude:\n  # - package2\n");
        $result = $method->invoke($plugin, $yamlFile, $binDir . '/generate-composer-require.yaml');
        $this->assertTrue($result, 'YAML with only commented packages should be considered empty');

        // Test with YAML that has packages in include section (should NOT be considered empty)
        file_put_contents($yamlFile, "ignore:\n  # - package1\ninclude:\n  - included/package\n");
        $result = $method->invoke($plugin, $yamlFile, $binDir . '/generate-composer-require.yaml');
        $this->assertFalse($result, 'YAML with packages in include section should NOT be considered empty');

        // Test with YAML that has packages in ignore section (should NOT be considered empty)
        file_put_contents($yamlFile, "ignore:\n  - ignored/package\ninclude:\n  # - package2\n");
        $result = $method->invoke($plugin, $yamlFile, $binDir . '/generate-composer-require.yaml');
        $this->assertFalse($result, 'YAML with packages in ignore section should NOT be considered empty');

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.yaml');
        @unlink($binDir . '/generate-composer-require.yaml');
        @rmdir($binDir);
        @rmdir($packageDir);
        @rmdir($vendorDir . '/nowo-tech');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testInstallFilesDoesNotCopyProcessUpdatesPhp(): void
    {
        $tempDir = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir = $tempDir . '/vendor';
        $packageDir = $vendorDir . '/nowo-tech/composer-update-helper';
        $binDir = $packageDir . '/bin';
        mkdir($binDir, 0777, true);

        // Create all files in vendor
        file_put_contents($binDir . '/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($binDir . '/generate-composer-require.yaml', '# YAML config');
        file_put_contents($binDir . '/process-updates.php', '<?php echo "processor";');

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
                $this->stringContains('Creating generate-composer-require.yaml')
            ));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostInstall($event);

        // Verify only .sh and .yaml are copied
        $this->assertFileExists($tempDir . '/generate-composer-require.sh');
        $this->assertFileExists($tempDir . '/generate-composer-require.yaml');
        
        // Verify process-updates.php is NOT copied (stays in vendor)
        $this->assertFileDoesNotExist($tempDir . '/process-updates.php');
        $this->assertFileExists($binDir . '/process-updates.php');

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/generate-composer-require.yaml');
        @unlink($binDir . '/generate-composer-require.sh');
        @unlink($binDir . '/generate-composer-require.yaml');
        @unlink($binDir . '/process-updates.php');
        @rmdir($binDir);
        @rmdir($packageDir);
        @rmdir($vendorDir . '/nowo-tech');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }
}
