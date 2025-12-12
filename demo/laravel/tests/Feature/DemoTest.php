<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoTest extends TestCase
{
    public function test_laravel_version(): void
    {
        $this->assertTrue(version_compare(PHP_VERSION, '8.5.0', '>='));
    }

    public function test_composer_update_helper_installed(): void
    {
        $composerJson = json_decode(file_get_contents(__DIR__ . '/../../composer.json'), true);
        $this->assertArrayHasKey('require-dev', $composerJson);
        $this->assertArrayHasKey('nowo-tech/composer-update-helper', $composerJson['require-dev']);
    }

    public function test_public_directory_exists(): void
    {
        $this->assertDirectoryExists(__DIR__ . '/../../public');
        $this->assertFileExists(__DIR__ . '/../../public/index.php');
    }

    public function test_composer_update_helper_script_installed(): void
    {
        $scriptPath = __DIR__ . '/../../generate-composer-require.sh';
        $this->assertFileExists($scriptPath, 'The generate-composer-require.sh script should be installed by the Composer plugin');
        $this->assertTrue(is_executable($scriptPath), 'The script should be executable');
    }
}

