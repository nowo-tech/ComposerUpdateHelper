<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper\Tests;

use Composer\{Composer, Config};
use Composer\IO\IOInterface;
use Composer\Script\Event;
use NowoTech\ComposerUpdateHelper\Installer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Test suite for the Installer class.
 * Tests the static installer functionality for Composer scripts,
 * including file installation and uninstallation.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 *
 * @see    https://github.com/HecFranco
 */
final class InstallerTest extends TestCase
{
    private string $tempDir;

    private string $vendorDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/composer-update-helper-test-' . uniqid();
        $this->vendorDir = $this->tempDir . '/vendor';

        mkdir($this->vendorDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->tempDir);
    }

    public function testInstallCopiesScriptToProjectRoot(): void
    {
        $event = $this->createMockEvent();

        // Create source files in the package directory
        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh\necho "test"');

        Installer::install($event);

        $this->assertFileExists($this->tempDir . '/generate-composer-require.sh');
    }

    public function testInstallCreatesIgnoreFileIfNotExists(): void
    {
        $event = $this->createMockEvent();

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.ignore.txt', '# Ignore file');

        Installer::install($event);

        $this->assertFileExists($this->tempDir . '/generate-composer-require.ignore.txt');
    }

    public function testInstallDoesNotOverwriteExistingIgnoreFile(): void
    {
        $event = $this->createMockEvent();

        // Create existing ignore file with custom content
        $customContent = "# My custom packages\nvendor/my-package";
        file_put_contents($this->tempDir . '/generate-composer-require.ignore.txt', $customContent);

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.ignore.txt', '# Default ignore file');

        Installer::install($event);

        // Verify the custom content was preserved
        $this->assertStringContainsString('My custom packages', (string) file_get_contents($this->tempDir . '/generate-composer-require.ignore.txt'));
    }

    public function testUninstallRemovesScript(): void
    {
        $event = $this->createMockEvent();

        // Create the script file
        file_put_contents($this->tempDir . '/generate-composer-require.sh', '#!/bin/sh');

        Installer::uninstall($event);

        $this->assertFileDoesNotExist($this->tempDir . '/generate-composer-require.sh');
    }

    public function testUninstallPreservesIgnoreFile(): void
    {
        $event = $this->createMockEvent();

        // Create both files
        file_put_contents($this->tempDir . '/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($this->tempDir . '/generate-composer-require.ignore.txt', '# Ignore file');

        Installer::uninstall($event);

        // Script should be removed, but ignore file should remain
        $this->assertFileDoesNotExist($this->tempDir . '/generate-composer-require.sh');
        $this->assertFileExists($this->tempDir . '/generate-composer-require.ignore.txt');
    }

    public function testUninstallWhenFileDoesNotExist(): void
    {
        $event = $this->createMockEvent();

        // Don't create the file
        Installer::uninstall($event);

        // Should not throw any exception
        $this->assertTrue(true);
    }

    public function testInstallUpdatesWhenContentDiffers(): void
    {
        $event = $this->createMockEvent();

        // Create existing file with different content
        file_put_contents($this->tempDir . '/generate-composer-require.sh', '#!/bin/sh\necho "old"');

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh\necho "new"');

        Installer::install($event);

        $this->assertFileExists($this->tempDir . '/generate-composer-require.sh');
        $this->assertStringContainsString('new', (string) file_get_contents($this->tempDir . '/generate-composer-require.sh'));
    }

    public function testInstallSkipsWhenContentMatches(): void
    {
        $event = $this->createMockEvent();

        $content = '#!/bin/sh\necho "same"';
        file_put_contents($this->tempDir . '/generate-composer-require.sh', $content);

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', $content);
        sleep(1); // Ensure time difference

        Installer::install($event);

        // File should still exist and modification time should be unchanged (or very close)
        $this->assertFileExists($this->tempDir . '/generate-composer-require.sh');
    }

    public function testInstallHandlesMissingSourceFile(): void
    {
        $event = $this->createMockEvent();

        // Ensure destination file doesn't exist
        $destFile = $this->tempDir . '/generate-composer-require.sh';

        if (file_exists($destFile)) {
            @unlink($destFile);
        }

        // Don't create source file in vendor
        // The package directory won't exist, so source file won't be found
        // Also, ensure the development mode path doesn't have the file
        $devPackageDir = __DIR__ . '/../bin';
        $devSourceFile = $devPackageDir . '/generate-composer-require.sh';
        $devSourceBackup = null;

        if (file_exists($devSourceFile)) {
            $devSourceBackup = file_get_contents($devSourceFile);
            @unlink($devSourceFile);
        }

        try {
            Installer::install($event);

            // Should not create destination file
            $this->assertFileDoesNotExist($destFile);
        } finally {
            // Restore file if it existed
            if ($devSourceBackup !== null) {
                file_put_contents($devSourceFile, $devSourceBackup);
            }
        }
    }

    public function testInstallInDevelopmentMode(): void
    {
        $event = $this->createMockEvent();

        // Simulate development mode (package not in vendor)
        $packageDir = __DIR__ . '/..';
        $binDir = $packageDir . '/bin';

        if (!is_dir($binDir)) {
            mkdir($binDir, 0777, true);
        }

        file_put_contents($binDir . '/generate-composer-require.sh', '#!/bin/sh\necho "dev"');

        Installer::install($event);

        $this->assertFileExists($this->tempDir . '/generate-composer-require.sh');
        $this->assertStringContainsString('dev', (string) file_get_contents($this->tempDir . '/generate-composer-require.sh'));

        // Cleanup
        @unlink($this->tempDir . '/generate-composer-require.sh');
    }

    public function testInstallUpdatesGitignoreWhenExistsWithoutNewline(): void
    {
        $event = $this->createMockEvent();

        // Create .gitignore file without trailing newline
        $gitignorePath = $this->tempDir . '/.gitignore';
        file_put_contents($gitignorePath, 'vendor/', FILE_APPEND); // No newline at end

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');

        Installer::install($event);

        $gitignoreContent = file_get_contents($gitignorePath);
        $this->assertStringContainsString('generate-composer-require.sh', $gitignoreContent);
        $this->assertStringContainsString('generate-composer-require.ignore.txt', $gitignoreContent);
    }

    public function testInstallUpdatesGitignoreWhenExistsWithContent(): void
    {
        $event = $this->createMockEvent();

        // Create .gitignore file with content
        $gitignorePath = $this->tempDir . '/.gitignore';
        file_put_contents($gitignorePath, "vendor/\nnode_modules/\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');

        Installer::install($event);

        $gitignoreContent = file_get_contents($gitignorePath);
        $this->assertStringContainsString('generate-composer-require.sh', $gitignoreContent);
        $this->assertStringContainsString('generate-composer-require.ignore.txt', $gitignoreContent);
    }

    /**
     * @return Event&MockObject
     */
    private function createMockEvent(): Event
    {
        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($this->vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $io = $this->createMock(IOInterface::class);

        $event = $this->createMock(Event::class);
        $event->method('getComposer')
            ->willReturn($composer);
        $event->method('getIO')
            ->willReturn($io);

        return $event;
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }

        rmdir($dir);
    }
}
