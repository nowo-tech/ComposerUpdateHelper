<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper\Tests;

use Composer\Composer;
use Composer\Config;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use NowoTech\ComposerUpdateHelper\Installer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Héctor Franco Aceituno <hectorfranco@nowo.com>
 *
 * @see    https://github.com/HecFranco
 */
class InstallerTest extends TestCase
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
        $this->assertStringContainsString('My custom packages', file_get_contents($this->tempDir . '/generate-composer-require.ignore.txt'));
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

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
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
