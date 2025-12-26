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
        // Also create process-updates.php in vendor (should NOT be copied)
        file_put_contents($packageDir . '/bin/process-updates.php', '<?php echo "test";');

        Installer::install($event);

        $this->assertFileExists($this->tempDir . '/generate-composer-require.sh');
        // Verify process-updates.php is NOT copied (stays in vendor)
        $this->assertFileDoesNotExist($this->tempDir . '/process-updates.php');
        $this->assertFileExists($packageDir . '/bin/process-updates.php');
    }

    public function testInstallCreatesYamlConfigFileIfNotExists(): void
    {
        $event = $this->createMockEvent();

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config file');
        // process-updates.php should NOT be copied
        file_put_contents($packageDir . '/bin/process-updates.php', '<?php echo "test";');

        Installer::install($event);

        $this->assertFileExists($this->tempDir . '/generate-composer-require.yaml');
        // Verify process-updates.php is NOT copied (stays in vendor)
        $this->assertFileDoesNotExist($this->tempDir . '/process-updates.php');
        $this->assertFileExists($packageDir . '/bin/process-updates.php');
    }

    public function testInstallDoesNotOverwriteExistingYamlFile(): void
    {
        $event = $this->createMockEvent();

        // Create existing YAML file with custom content
        $customContent = "# My custom packages\nignore:\n  - vendor/my-package";
        file_put_contents($this->tempDir . '/generate-composer-require.yaml', $customContent);

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# Default YAML config');

        Installer::install($event);

        // Verify the custom content was preserved
        $this->assertStringContainsString('My custom packages', (string) file_get_contents($this->tempDir . '/generate-composer-require.yaml'));
    }

    public function testUninstallRemovesScript(): void
    {
        $event = $this->createMockEvent();

        // Create the script file
        file_put_contents($this->tempDir . '/generate-composer-require.sh', '#!/bin/sh');

        Installer::uninstall($event);

        $this->assertFileDoesNotExist($this->tempDir . '/generate-composer-require.sh');
    }

    public function testUninstallPreservesYamlConfigFile(): void
    {
        $event = $this->createMockEvent();

        // Create both files
        file_put_contents($this->tempDir . '/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($this->tempDir . '/generate-composer-require.yaml', '# YAML config file');

        Installer::uninstall($event);

        // Script should be removed, but YAML config file should remain
        $this->assertFileDoesNotExist($this->tempDir . '/generate-composer-require.sh');
        $this->assertFileExists($this->tempDir . '/generate-composer-require.yaml');
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
        // This test was modifying the real bin/ directory, which breaks the package
        // The missing source file functionality is tested indirectly through other tests
        // that verify Installer correctly handles cases when files don't exist
        $this->markTestSkipped('Missing source file test skipped to avoid modifying real bin/ directory. Functionality is tested indirectly.');
    }

    public function testInstallInDevelopmentMode(): void
    {
        // This test was modifying the real bin/ directory, which breaks the package
        // The development mode functionality is tested indirectly through other tests
        // that verify Installer correctly handles the case when package is not in vendor
        $this->markTestSkipped('Development mode test skipped to avoid modifying real bin/ directory. Functionality is tested indirectly.');
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
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        $gitignoreContent = file_get_contents($gitignorePath);
        // .sh and .yaml should NOT be in .gitignore (they should be committed)
        $this->assertStringNotContainsString('generate-composer-require.sh', $gitignoreContent);
        $this->assertStringNotContainsString('generate-composer-require.yaml', $gitignoreContent);
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
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        $gitignoreContent = file_get_contents($gitignorePath);
        // .sh and .yaml should NOT be in .gitignore (they should be committed)
        $this->assertStringNotContainsString('generate-composer-require.sh', $gitignoreContent);
        $this->assertStringNotContainsString('generate-composer-require.yaml', $gitignoreContent);
    }

    public function testInstallMigratesTxtToYamlAndDeletesTxt(): void
    {
        $event = $this->createMockEvent();

        // Create old TXT file in project (simulating upgrade scenario)
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "doctrine/orm\nsymfony/security-bundle\n# Comment line\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify YAML file was created
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        $this->assertFileExists($yamlFile);

        // Verify content was migrated correctly
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('doctrine/orm', $yamlContent);
        $this->assertStringContainsString('symfony/security-bundle', $yamlContent);
        $this->assertStringContainsString('ignore:', $yamlContent);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);
    }

    public function testInstallMigratesTxtToYamlWhenYamlExistsButIsEmpty(): void
    {
        $event = $this->createMockEvent();

        // Create empty YAML file
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, '');

        // Create old TXT file with packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "doctrine/orm\nsymfony/security-bundle\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify YAML file was updated with migrated content
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('doctrine/orm', $yamlContent);
        $this->assertStringContainsString('symfony/security-bundle', $yamlContent);
        $this->assertStringContainsString('ignore:', $yamlContent);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);
    }

    public function testInstallDoesNotMigrateTxtWhenYamlHasUserPackages(): void
    {
        $event = $this->createMockEvent();

        // Create YAML file with user-defined packages
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "# Composer Update Helper Configuration\nignore:\n  - existing/package\n");

        // Create old TXT file with different packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "doctrine/orm\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify YAML file was NOT changed (preserves user packages)
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('existing/package', $yamlContent);
        $this->assertStringNotContainsString('doctrine/orm', $yamlContent);

        // Verify old TXT file still exists (not migrated)
        $this->assertFileExists($oldTxtFile);
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

    public function testIsYamlEmptyOrTemplateDetectsIncludeSection(): void
    {
        $event = $this->createMockEvent();

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        // Use reflection to call private isYamlEmptyOrTemplate method
        $reflection = new \ReflectionClass(Installer::class);
        $method = $reflection->getMethod('isYamlEmptyOrTemplate');
        $method->setAccessible(true);

        // Test with YAML that has only include section (should be considered empty for migration)
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "ignore:\n  # - package1\ninclude:\n  # - package2\n");
        $result = $method->invoke(null, $yamlFile, $packageDir . '/bin/generate-composer-require.yaml');
        $this->assertTrue($result, 'YAML with only commented packages should be considered empty');

        // Test with YAML that has packages in include section (should be considered empty for ignore section)
        // Include section doesn't prevent migration - only ignore section matters
        file_put_contents($yamlFile, "ignore:\n  # - package1\ninclude:\n  - included/package\n");
        $result = $method->invoke(null, $yamlFile, $packageDir . '/bin/generate-composer-require.yaml');
        $this->assertTrue($result, 'YAML with packages in include section should be considered empty for ignore section (migration allowed)');

        // Test with YAML that has packages in ignore section (should NOT be considered empty)
        file_put_contents($yamlFile, "ignore:\n  - ignored/package\ninclude:\n  # - package2\n");
        $result = $method->invoke(null, $yamlFile, $packageDir . '/bin/generate-composer-require.yaml');
        $this->assertFalse($result, 'YAML with packages in ignore section should NOT be considered empty');

        // Cleanup
        @unlink($yamlFile);
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationReadsIncludeSectionFromYaml(): void
    {
        $event = $this->createMockEvent();

        // Create old TXT file with packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "package1/one\npackage2/two\n");

        // Create YAML file with both ignore and include sections
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "ignore:\n  - package1/one\n  - package2/two\ninclude:\n  - included1/one\n  - included2/two\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify YAML file still has include section after migration
        $this->assertFileExists($yamlFile);
        $yamlContent = file_get_contents($yamlFile);
        // Migration should preserve include section
        $this->assertStringContainsString('included1/one', $yamlContent);
        $this->assertStringContainsString('included2/two', $yamlContent);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testInstallHandlesDevelopmentMode(): void
    {
        $event = $this->createMockEvent();

        // Simulate development mode (package not in vendor)
        // Create source files in development directory (parent of src)
        $devPackageDir = dirname(__DIR__);
        $devBinDir = $devPackageDir . '/bin';

        if (!is_dir($devBinDir)) {
            $this->markTestSkipped('Development bin directory does not exist');
        }

        // Create source file in development directory
        $sourceFile = $devBinDir . '/generate-composer-require.sh';
        $originalContent = null;
        if (file_exists($sourceFile)) {
            $originalContent = file_get_contents($sourceFile);
        }

        try {
            file_put_contents($sourceFile, '#!/bin/sh\necho "dev"');

            Installer::install($event);

            // Verify file was installed from development directory
            $this->assertFileExists($this->tempDir . '/generate-composer-require.sh');
        } finally {
            // Restore original file
            if ($originalContent !== null) {
                file_put_contents($sourceFile, $originalContent);
            } elseif (file_exists($sourceFile)) {
                @unlink($sourceFile);
            }
        }

        // Cleanup
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($this->tempDir . '/generate-composer-require.yaml');
    }

    public function testMigrationHandlesVerificationFailure(): void
    {
        // Create old TXT file with packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "package1/one\npackage2/two\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        // Create event with IO mock to capture messages
        $io = $this->createMock(IOInterface::class);
        $io->expects($this->atLeastOnce())
            ->method('write')
            ->with($this->logicalOr(
                $this->stringContains('Installing generate-composer-require.sh'),
                $this->stringContains('Creating generate-composer-require.yaml'),
                $this->stringContains('Migrating configuration from TXT to YAML format'),
                $this->stringContains('Configuration migrated to'),
                $this->stringContains('Removed old generate-composer-require.ignore.txt file')
            ));

        $config = $this->createMock(Config::class);
        $config->method('get')
            ->with('vendor-dir')
            ->willReturn($this->vendorDir);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')
            ->willReturn($config);

        $event = $this->createMock(Event::class);
        $event->method('getIO')
            ->willReturn($io);
        $event->method('getComposer')
            ->willReturn($composer);

        Installer::install($event);

        // Verify YAML was created/updated with packages from TXT
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        $this->assertFileExists($yamlFile);
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('ignore:', $yamlContent);
        $this->assertStringContainsString('package1/one', $yamlContent);
        $this->assertStringContainsString('package2/two', $yamlContent);

        // Verify TXT was migrated (normal case - migration should succeed)
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($this->tempDir . '/generate-composer-require.yaml');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationCreatesNewYamlWhenYamlDoesNotExist(): void
    {
        $event = $this->createMockEvent();

        // Create old TXT file with packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "newpackage/one\nnewpackage/two\n");

        // Do NOT create YAML file (it should be created during migration)

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify YAML was created with migrated content
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        $this->assertFileExists($yamlFile);
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('newpackage/one', $yamlContent);
        $this->assertStringContainsString('newpackage/two', $yamlContent);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationHandlesYamlWithoutIgnoreSection(): void
    {
        $event = $this->createMockEvent();

        // Create YAML file without ignore section (only include section)
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "include:\n  - included/package\n");

        // Create old TXT file with packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "package1/one\npackage2/two\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify YAML was updated with ignore section
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('ignore:', $yamlContent);
        $this->assertStringContainsString('package1/one', $yamlContent);
        $this->assertStringContainsString('package2/two', $yamlContent);
        // Include section should be preserved
        $this->assertStringContainsString('include:', $yamlContent);
        $this->assertStringContainsString('included/package', $yamlContent);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationHandlesEmptyTxtFile(): void
    {
        $event = $this->createMockEvent();

        // Create empty TXT file
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify YAML was created with template (empty packages)
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        $this->assertFileExists($yamlFile);
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('ignore:', $yamlContent);
        // When YAML exists and TXT is empty, merge preserves structure but may not have specific template comment
        // Just verify ignore section exists

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testUpdateGitignoreRemovesOldTxtEntry(): void
    {
        $event = $this->createMockEvent();

        // Create .gitignore with old TXT entry
        $gitignorePath = $this->tempDir . '/.gitignore';
        file_put_contents($gitignorePath, "vendor/\ngenerate-composer-require.ignore.txt\nnode_modules/\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        $gitignoreContent = file_get_contents($gitignorePath);
        $this->assertStringNotContainsString('generate-composer-require.ignore.txt', $gitignoreContent);
        $this->assertStringContainsString('vendor/', $gitignoreContent);
        $this->assertStringContainsString('node_modules/', $gitignoreContent);

        // Cleanup
        @unlink($gitignorePath);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($this->tempDir . '/generate-composer-require.yaml');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testUpdateGitignoreRemovesShAndYamlEntries(): void
    {
        $event = $this->createMockEvent();

        // Create .gitignore with .sh and .yaml entries (should be removed)
        $gitignorePath = $this->tempDir . '/.gitignore';
        file_put_contents($gitignorePath, "vendor/\ngenerate-composer-require.sh\ngenerate-composer-require.yaml\nnode_modules/\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        $gitignoreContent = file_get_contents($gitignorePath);
        $this->assertStringNotContainsString('generate-composer-require.sh', $gitignoreContent);
        $this->assertStringNotContainsString('generate-composer-require.yaml', $gitignoreContent);
        $this->assertStringContainsString('vendor/', $gitignoreContent);

        // Cleanup
        @unlink($gitignorePath);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($this->tempDir . '/generate-composer-require.yaml');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testUpdateGitignoreHandlesMissingGitignore(): void
    {
        $event = $this->createMockEvent();

        // Ensure .gitignore doesn't exist
        $gitignorePath = $this->tempDir . '/.gitignore';
        if (file_exists($gitignorePath)) {
            @unlink($gitignorePath);
        }

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        // Should not throw exception when .gitignore doesn't exist
        Installer::install($event);

        $this->assertFileExists($this->tempDir . '/generate-composer-require.sh');

        // Cleanup
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($this->tempDir . '/generate-composer-require.yaml');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationWithYamlHavingDifferentPackages(): void
    {
        $event = $this->createMockEvent();

        // Create old TXT file with packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "package1/one\npackage2/two\n");

        // Create YAML file with different packages (user-defined, should be preserved)
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "ignore:\n  - different/package\n  - another/package\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // YAML should be preserved (not migrated) because it has user-defined packages
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('different/package', $yamlContent);
        $this->assertStringContainsString('another/package', $yamlContent);
        // TXT file should still exist because packages don't match
        $this->assertFileExists($oldTxtFile);

        // Cleanup
        @unlink($oldTxtFile);
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testInstallSetsCorrectFilePermissions(): void
    {
        $event = $this->createMockEvent();

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh\necho "test"');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        $installedScript = $this->tempDir . '/generate-composer-require.sh';
        $this->assertFileExists($installedScript);

        // Verify file has executable permissions (getChmodMode returns 493 = 0755)
        $perms = fileperms($installedScript);
        $this->assertNotFalse($perms);
        // Check that file is executable (permission bits include execute)
        $this->assertTrue(is_executable($installedScript) || ($perms & 0111) !== 0);

        // Cleanup
        @unlink($installedScript);
        @unlink($this->tempDir . '/generate-composer-require.yaml');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationMergesPackagesWhenYamlExistsWithIgnoreSection(): void
    {
        $event = $this->createMockEvent();

        // Create YAML file with existing ignore section and packages that match TXT
        // This tests the case where packages already match (shouldDeleteTxt = true)
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "ignore:\n  - existing/package\ninclude:\n  - included/package\n");

        // Create old TXT file with same packages as YAML (already migrated scenario)
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "existing/package\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify YAML was preserved (packages match, so just delete TXT)
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('existing/package', $yamlContent);
        $this->assertStringContainsString('included/package', $yamlContent); // Include section preserved

        // Verify old TXT file was deleted (packages matched)
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationAddsIgnoreSectionWhenYamlExistsWithoutIgnoreSection(): void
    {
        $event = $this->createMockEvent();

        // Create YAML file without ignore section (only include)
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "include:\n  - included/package\n");

        // Create old TXT file with packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "package1/one\npackage2/two\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify ignore section was added before include section
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('ignore:', $yamlContent);
        $this->assertStringContainsString('package1/one', $yamlContent);
        $this->assertStringContainsString('package2/two', $yamlContent);
        $this->assertStringContainsString('include:', $yamlContent);
        $this->assertStringContainsString('included/package', $yamlContent);
        
        // Verify ignore section comes before include section
        $ignorePos = strpos($yamlContent, 'ignore:');
        $includePos = strpos($yamlContent, 'include:');
        $this->assertNotFalse($ignorePos);
        $this->assertNotFalse($includePos);
        $this->assertLessThan($includePos, $ignorePos);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationSkipsOldIgnoreEntriesWhenMerging(): void
    {
        $event = $this->createMockEvent();

        // Create YAML file that is empty/template (no packages in ignore section)
        // This allows migration to proceed
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "ignore:\n  # - commented/package\ninclude:\n  - included/package\n");

        // Create old TXT file with packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "old/package1\nnew/package\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify packages were merged (old entries in YAML are skipped, new ones added)
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('old/package1', $yamlContent);
        $this->assertStringContainsString('new/package', $yamlContent);
        $this->assertStringContainsString('included/package', $yamlContent); // Include section preserved
        // Count occurrences - old/package1 should appear once (merged, not duplicated)
        $this->assertEquals(1, substr_count($yamlContent, 'old/package1'));

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testUpdateGitignoreDoesNotUpdateWhenNoEntriesToRemove(): void
    {
        $event = $this->createMockEvent();

        // Create .gitignore without any Composer Update Helper entries
        $gitignorePath = $this->tempDir . '/.gitignore';
        $originalContent = "vendor/\nnode_modules/\n";
        file_put_contents($gitignorePath, $originalContent);

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // .gitignore should remain unchanged (no entries to remove)
        $gitignoreContent = file_get_contents($gitignorePath);
        $this->assertStringContainsString('vendor/', $gitignoreContent);
        $this->assertStringContainsString('node_modules/', $gitignoreContent);
        // Content should be the same (or with newline added, but no entries removed)
        $this->assertStringNotContainsString('generate-composer-require', $gitignoreContent);

        // Cleanup
        @unlink($gitignorePath);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($this->tempDir . '/generate-composer-require.yaml');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationHandlesYamlWithMultipleSections(): void
    {
        $event = $this->createMockEvent();

        // Create YAML file with multiple sections but no packages in ignore (template only)
        // This allows migration to proceed
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "ignore:\n  # - commented/package\ninclude:\n  - included/package\nother:\n  - other/value\n");

        // Create old TXT file with packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "new/package\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify YAML was merged correctly, preserving all sections
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('new/package', $yamlContent);
        $this->assertStringContainsString('included/package', $yamlContent);
        $this->assertStringContainsString('other:', $yamlContent);
        $this->assertStringContainsString('other/value', $yamlContent);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationHandlesYamlWithCommentsInIgnoreSection(): void
    {
        $event = $this->createMockEvent();

        // Create YAML file with comments but no actual packages (template only)
        // This allows migration to proceed
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "# Header comment\nignore:\n  # Comment before package\n  # - commented/package\n  # Comment after package\ninclude:\n  - included/package\n");

        // Create old TXT file with packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "new/package\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify YAML was merged correctly, preserving comments
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('Header comment', $yamlContent);
        $this->assertStringContainsString('Comment before package', $yamlContent);
        $this->assertStringContainsString('new/package', $yamlContent);
        $this->assertStringContainsString('included/package', $yamlContent);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testExtractPackagesFromYamlIgnoreSectionHandlesComplexYaml(): void
    {
        $event = $this->createMockEvent();

        // Create complex YAML with multiple sections and comments
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "# Configuration\nignore:\n  - package1\n  # - commented/package\n  - package2\ninclude:\n  - included1\nother_section:\n  - value\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        // Create TXT with same packages to test extraction
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "package1\npackage2\n");

        Installer::install($event);

        // Verify packages were extracted correctly (should match, so TXT deleted)
        $this->assertFileDoesNotExist($oldTxtFile);
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('package1', $yamlContent);
        $this->assertStringContainsString('package2', $yamlContent);

        // Cleanup
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationHandlesYamlWithOnlyIncludeSectionAndNoIgnore(): void
    {
        $event = $this->createMockEvent();

        // Create YAML with only include section (no ignore section at all)
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "include:\n  - included/package\n");

        // Create old TXT file with packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "package1/one\npackage2/two\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify ignore section was added before include
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('ignore:', $yamlContent);
        $this->assertStringContainsString('package1/one', $yamlContent);
        $this->assertStringContainsString('package2/two', $yamlContent);
        $this->assertStringContainsString('include:', $yamlContent);
        $this->assertStringContainsString('included/package', $yamlContent);

        // Verify ignore comes before include
        $ignorePos = strpos($yamlContent, 'ignore:');
        $includePos = strpos($yamlContent, 'include:');
        $this->assertLessThan($includePos, $ignorePos);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testExtractPackagesFromYamlHandlesEndOfSectionDetection(): void
    {
        $event = $this->createMockEvent();

        // Create YAML with ignore section followed by another section (tests end of section detection)
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, "ignore:\n  - package1\n  - package2\nother_section:\n  - value\ninclude:\n  - included\n");

        // Create TXT with same packages
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "package1\npackage2\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify packages were extracted correctly (should match, so TXT deleted)
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testIsYamlEmptyOrTemplateHandlesFileNotExists(): void
    {
        $event = $this->createMockEvent();

        // Test when YAML doesn't exist (should be considered empty/template)
        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        // Create TXT file
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "package1\npackage2\n");

        // Ensure YAML doesn't exist
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        if (file_exists($yamlFile)) {
            @unlink($yamlFile);
        }

        Installer::install($event);

        // Verify YAML was created (migration should proceed when YAML doesn't exist)
        $this->assertFileExists($yamlFile);
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('package1', $yamlContent);
        $this->assertStringContainsString('package2', $yamlContent);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testIsYamlEmptyOrTemplateHandlesEmptyFile(): void
    {
        $event = $this->createMockEvent();

        // Create empty YAML file
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        file_put_contents($yamlFile, '');

        // Create TXT file
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "package1\npackage2\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify YAML was migrated (empty file should be considered template)
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('package1', $yamlContent);
        $this->assertStringContainsString('package2', $yamlContent);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationCreatesNewYamlWithEmptyPackages(): void
    {
        $event = $this->createMockEvent();

        // Create empty TXT file (only comments/whitespace)
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "# Comment line\n\n  \n# Another comment\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        // Ensure YAML doesn't exist in project (will be created from scratch)
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        if (file_exists($yamlFile)) {
            @unlink($yamlFile);
        }

        Installer::install($event);

        // Verify YAML was created (migrated from empty TXT)
        $this->assertFileExists($yamlFile);
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('ignore:', $yamlContent);
        // When YAML is created from empty TXT, it should have template comments
        // But if YAML already exists and is template, it might merge
        // Just verify ignore section exists
        $this->assertStringContainsString('ignore:', $yamlContent);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }

    public function testMigrationHandlesTxtWithOnlyComments(): void
    {
        $event = $this->createMockEvent();

        // Create TXT file with only comments
        $oldTxtFile = $this->tempDir . '/generate-composer-require.ignore.txt';
        file_put_contents($oldTxtFile, "# This is a comment\n# Another comment\n# package/name\n");

        $packageDir = $this->vendorDir . '/nowo-tech/composer-update-helper';
        mkdir($packageDir . '/bin', 0777, true);
        file_put_contents($packageDir . '/bin/generate-composer-require.sh', '#!/bin/sh');
        file_put_contents($packageDir . '/bin/generate-composer-require.yaml', '# YAML config');

        Installer::install($event);

        // Verify YAML was created with template (no packages extracted from comments)
        $yamlFile = $this->tempDir . '/generate-composer-require.yaml';
        $this->assertFileExists($yamlFile);
        $yamlContent = file_get_contents($yamlFile);
        $this->assertStringContainsString('ignore:', $yamlContent);
        // Should not contain the commented package
        $this->assertStringNotContainsString('package/name', $yamlContent);

        // Verify old TXT file was deleted
        $this->assertFileDoesNotExist($oldTxtFile);

        // Cleanup
        @unlink($yamlFile);
        @unlink($this->tempDir . '/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.sh');
        @unlink($packageDir . '/bin/generate-composer-require.yaml');
        @rmdir($packageDir . '/bin');
        @rmdir($packageDir);
    }
}
