<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper\Tests\Unit;

use ConfigLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function dirname;

/**
 * @internal
 */
#[CoversClass(ConfigLoader::class)]
final class ConfigLoaderTest extends TestCase
{
    private string $file;

    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/bin/lib/ConfigLoader.php';
        $this->file = (string) tempnam(sys_get_temp_dir(), 'cuh');
    }

    protected function tearDown(): void
    {
        @unlink($this->file);
    }

    public function testReadPackagesFromYamlStripsInlineComments(): void
    {
        $this->write(<<<'YAML'
            ignore:
              # Full-line comment
              - rector/swiss-knife                  # 2.7.0 — regression
              - phpdocumentor/reflection-docblock #:6.0.1
              - mongodb/mongodb
              # - commented/package                 # not read
              - 'quoted/package'   # quoted + comment
              - "double/quoted"
              - vendor/hash#name

            include:
              - forced/package  # reason
            YAML);

        self::assertSame(
            [
                'rector/swiss-knife',
                'phpdocumentor/reflection-docblock',
                'mongodb/mongodb',
                'quoted/package',
                'double/quoted',
                'vendor/hash#name',
            ],
            ConfigLoader::readPackagesFromYaml($this->file, 'ignore'),
        );
        self::assertSame(['forced/package'], ConfigLoader::readPackagesFromYaml($this->file, 'include'));
    }

    public function testReadPackagesFromYamlSkipsCommentOnlyEntries(): void
    {
        $this->write("ignore:\n  - # only a comment\n  - real/package\n");

        self::assertSame(['real/package'], ConfigLoader::readPackagesFromYaml($this->file, 'ignore'));
    }

    public function testReadConfigValueStripsInlineComments(): void
    {
        $this->write(<<<'YAML'
            language: es   # Spanish
            check-dependencies: true # enabled
            retries: 3  # times
            quoted: 'a # b'   # comment
            quoted-bool: "false"
            empty: # nothing
            YAML);

        self::assertSame('es', ConfigLoader::readConfigValue($this->file, 'language'));
        self::assertTrue(ConfigLoader::readConfigValue($this->file, 'check-dependencies'));
        self::assertSame(3, ConfigLoader::readConfigValue($this->file, 'retries'));
        self::assertSame('a # b', ConfigLoader::readConfigValue($this->file, 'quoted'));
        self::assertSame('false', ConfigLoader::readConfigValue($this->file, 'quoted-bool'));
        self::assertSame('fallback', ConfigLoader::readConfigValue($this->file, 'empty', 'fallback'));
    }

    public function testReadPackagesFromTxtStripsInlineComments(): void
    {
        $this->write("# header\nvendor/a  # reason\nvendor/b\n\n");

        self::assertSame(['vendor/a', 'vendor/b'], ConfigLoader::readPackagesFromTxt($this->file));
    }

    private function write(string $content): void
    {
        file_put_contents($this->file, $content);
    }
}
