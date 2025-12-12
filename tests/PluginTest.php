<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper\Tests;

use Composer\{Composer, Config};
use Composer\IO\IOInterface;
use Composer\Script\{Event, ScriptEvents};
use NowoTech\ComposerUpdateHelper\Plugin;
use PHPUnit\Framework\TestCase;

/**
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
        $plugin   = new Plugin();
        $composer = $this->createMock(Composer::class);
        $io       = $this->createMock(IOInterface::class);

        // Should not throw any exception
        $plugin->activate($composer, $io);

        $this->assertTrue(true);
    }

    public function testDeactivateDoesNothing(): void
    {
        $plugin   = new Plugin();
        $composer = $this->createMock(Composer::class);
        $io       = $this->createMock(IOInterface::class);

        // Should not throw any exception
        $plugin->deactivate($composer, $io);

        $this->assertTrue(true);
    }

    public function testUninstallRemovesFiles(): void
    {
        $tempDir   = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
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
        $tempDir    = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir  = $tempDir . '/vendor';
        $packageDir = __DIR__ . '/..';
        mkdir($vendorDir, 0777, true);

        // Create source file in package
        $binDir = $packageDir . '/bin';

        if (!is_dir($binDir))
        {
            mkdir($binDir, 0777, true);
        }

        $sourceFile = $binDir . '/generate-composer-require.sh';

        // Backup original file if it exists
        $originalContent = null;

        if (file_exists($sourceFile))
        {
            $originalContent = file_get_contents($sourceFile);
        }

        try {
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
                  $this->stringContains('Creating generate-composer-require.ignore.txt')
                ));

            $event = $this->createMock(Event::class);
            $event->method('getIO')
                ->willReturn($io);

            $plugin = new Plugin();
            $plugin->activate($composer, $io);
            $plugin->onPostInstall($event);

            $this->assertFileExists($tempDir . '/generate-composer-require.sh');
        }
        finally
        {
            // Restore original file
            if ($originalContent !== null)
            {
                file_put_contents($sourceFile, $originalContent);
            }
            elseif (file_exists($sourceFile))
            {
                @unlink($sourceFile);
            }
        }

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/generate-composer-require.ignore.txt');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testOnPostUpdateInstallsFiles(): void
    {
        $tempDir    = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir  = $tempDir . '/vendor';
        $packageDir = __DIR__ . '/..';
        mkdir($vendorDir, 0777, true);

        // Create source file in package
        $binDir = $packageDir . '/bin';

        if (!is_dir($binDir))
        {
            mkdir($binDir, 0777, true);
        }

        $sourceFile = $binDir . '/generate-composer-require.sh';

        // Backup original file if it exists
        $originalContent = null;

        if (file_exists($sourceFile))
        {
            $originalContent = file_get_contents($sourceFile);
        }

        try {
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
                  $this->stringContains('Creating generate-composer-require.ignore.txt')
                ));

            $event = $this->createMock(Event::class);
            $event->method('getIO')
                ->willReturn($io);

            $plugin = new Plugin();
            $plugin->activate($composer, $io);
            $plugin->onPostUpdate($event);

            $this->assertFileExists($tempDir . '/generate-composer-require.sh');
        }
        finally
        {
            // Restore original file
            if ($originalContent !== null)
            {
                file_put_contents($sourceFile, $originalContent);
            }
            elseif (file_exists($sourceFile))
            {
                @unlink($sourceFile);
            }
        }

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testInstallFilesUpdatesWhenContentDiffers(): void
    {
        $tempDir    = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir  = $tempDir . '/vendor';
        $packageDir = __DIR__ . '/..';
        mkdir($vendorDir, 0777, true);

        // Create existing file with different content
        file_put_contents($tempDir . '/generate-composer-require.sh', '#!/bin/sh\necho "old"');

        // Create source file in package with new content
        $binDir = $packageDir . '/bin';

        if (!is_dir($binDir))
        {
            mkdir($binDir, 0777, true);
        }

        $sourceFile = $binDir . '/generate-composer-require.sh';

        // Backup original file if it exists
        $originalContent = null;

        if (file_exists($sourceFile))
        {
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
                  $this->stringContains('Creating generate-composer-require.ignore.txt')
                ));

            $event = $this->createMock(Event::class);
            $event->method('getIO')
                ->willReturn($io);

            $plugin = new Plugin();
            $plugin->activate($composer, $io);
            $plugin->onPostInstall($event);

            $this->assertFileExists($tempDir . '/generate-composer-require.sh');
            $this->assertStringContainsString('new', (string) file_get_contents($tempDir . '/generate-composer-require.sh'));
        }
        finally
        {
            // Restore original file
            if ($originalContent !== null)
            {
                file_put_contents($sourceFile, $originalContent);
            }
            elseif (file_exists($sourceFile))
            {
                @unlink($sourceFile);
            }
        }

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/generate-composer-require.ignore.txt');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testInstallFilesSkipsWhenContentMatches(): void
    {
        $tempDir    = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir  = $tempDir . '/vendor';
        $packageDir = __DIR__ . '/..';
        mkdir($vendorDir, 0777, true);

        $content = '#!/bin/sh\necho "same"';
        file_put_contents($tempDir . '/generate-composer-require.sh', $content);

        // Create source file in package with same content
        $binDir = $packageDir . '/bin';

        if (!is_dir($binDir))
        {
            mkdir($binDir, 0777, true);
        }

        $sourceFile = $binDir . '/generate-composer-require.sh';
        file_put_contents($sourceFile, $content);

        // Ensure ignore file doesn't exist to avoid triggering its creation
        $ignoreSource = $binDir . '/generate-composer-require.ignore.txt';

        if (file_exists($ignoreSource))
        {
            @unlink($ignoreSource);
        }

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);
        $io->expects($this->never())
            ->method('write')
            ->with($this->logicalOr(
              $this->stringContains('Updating'),
              $this->stringContains('Installing')
            ));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostInstall($event);

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }

    public function testInstallFilesHandlesMissingSourceFile(): void
    {
        $tempDir    = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir  = $tempDir . '/vendor';
        $packageDir = __DIR__ . '/..';
        mkdir($vendorDir, 0777, true);

        // Ensure source file doesn't exist
        $binDir     = $packageDir . '/bin';
        $sourceFile = $binDir . '/generate-composer-require.sh';

        if (file_exists($sourceFile))
        {
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

    public function testInstallFilesCreatesIgnoreFileIfNotExists(): void
    {
        $tempDir    = sys_get_temp_dir() . '/composer-update-helper-plugin-test-' . uniqid();
        $vendorDir  = $tempDir . '/vendor';
        $packageDir = __DIR__ . '/..';
        mkdir($vendorDir, 0777, true);

        // Create source files in package
        $binDir = $packageDir . '/bin';

        if (!is_dir($binDir))
        {
            mkdir($binDir, 0777, true);
        }

        file_put_contents($binDir . '/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($binDir . '/generate-composer-require.ignore.txt', '# Ignore file');

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
              $this->stringContains('Creating generate-composer-require.ignore.txt')
            ));

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);

        $plugin = new Plugin();
        $plugin->activate($composer, $io);
        $plugin->onPostInstall($event);

        $this->assertFileExists($tempDir . '/generate-composer-require.ignore.txt');

        // Cleanup
        @unlink($tempDir . '/generate-composer-require.sh');
        @unlink($tempDir . '/generate-composer-require.ignore.txt');
        @rmdir($vendorDir);
        @rmdir($tempDir);
    }
}
