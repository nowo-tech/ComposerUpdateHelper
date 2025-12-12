<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class DemoTest extends TestCase
{
    public function testSymfonyVersion(): void
    {
        $this->assertTrue(version_compare(PHP_VERSION, '8.5.0', '>='));
    }

    public function testComposerUpdateHelperInstalled(): void
    {
        $composerJson = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);
        $this->assertArrayHasKey('require-dev', $composerJson);
        $this->assertArrayHasKey('nowo-tech/composer-update-helper', $composerJson['require-dev']);
    }

    public function testPublicDirectoryExists(): void
    {
        $this->assertDirectoryExists(__DIR__ . '/../public');
        $this->assertFileExists(__DIR__ . '/../public/index.php');
    }

    public function testComposerUpdateHelperScriptInstalled(): void
    {
        $scriptPath = __DIR__ . '/../generate-composer-require.sh';
        $this->assertFileExists($scriptPath, 'The generate-composer-require.sh script should be installed by the Composer plugin');
        $this->assertTrue(is_executable($scriptPath), 'The script should be executable');
    }
}

