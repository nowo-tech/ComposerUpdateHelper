<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

use function dirname;

use const PHP_BINARY;

/**
 * Runs bin/generate-composer-require.sh and bin/process-updates.php against a fake `composer`
 * to check how failed or invalid `composer outdated` output is reported.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 *
 * @see    https://github.com/HecFranco
 */
final class OutdatedFailureHandlingTest extends TestCase
{
    private string $workDir;

    private string $projectDir;

    protected function setUp(): void
    {
        // Quote in the path: values must reach PHP as argv, never as part of a `php -r` source string
        $this->workDir    = sys_get_temp_dir() . "/cuh-outdated-it's-" . uniqid();
        $this->projectDir = $this->workDir . '/project';
        mkdir($this->projectDir, 0777, true);
        mkdir($this->workDir . '/bin', 0777, true);
        mkdir($this->workDir . '/tmp', 0777, true);

        file_put_contents($this->projectDir . '/composer.json', '{"require": {}}');

        // Fake composer: run as "php composer outdated ..."; behaviour driven by environment variables
        file_put_contents($this->workDir . '/bin/composer', <<<'PHP'
            #!/usr/bin/env php
            <?php
            fwrite(STDERR, (string) getenv('FAKE_COMPOSER_STDERR'));
            echo (string) getenv('FAKE_COMPOSER_STDOUT');
            exit((int) getenv('FAKE_COMPOSER_EXIT'));
            PHP);
        chmod($this->workDir . '/bin/composer', 0755);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->workDir);
    }

    public function testComposerFailureIsReportedAndPropagated(): void
    {
        $process = $this->runWrapper('', "Your requirements could not be resolved\n", 3);

        $this->assertSame(3, $process->getExitCode());
        $this->assertStringContainsString('composer outdated failed (exit code 3)', $process->getErrorOutput());
        $this->assertStringContainsString('Your requirements could not be resolved', $process->getErrorOutput());
    }

    public function testEmptyComposerOutputIsAnError(): void
    {
        $process = $this->runWrapper('', '', 0);

        $this->assertSame(1, $process->getExitCode());
        $this->assertStringContainsString('composer outdated returned no JSON output', $process->getErrorOutput());
    }

    public function testInvalidComposerOutputMakesTheWrapperFail(): void
    {
        $process = $this->runWrapper('this is not json', '', 0);

        $this->assertSame(1, $process->getExitCode());
        $this->assertStringContainsString('no JSON object found', $process->getErrorOutput());
        $this->assertStringContainsString('process-updates.php failed (exit code 1)', $process->getErrorOutput());
    }

    public function testStderrNoiseDoesNotCorruptTheJson(): void
    {
        // stderr notices must stay out of the JSON handed to process-updates.php
        $process = $this->runWrapper('{"installed": []}', "Deprecation Notice: {legacy} option\n", 0);

        $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        $this->assertStringContainsString('✅', $process->getOutput());
    }

    public function testYamlConfigValuesAreReadThroughPhpArgv(): void
    {
        file_put_contents($this->projectDir . '/generate-composer-require.yaml', "verbose: true\n");

        $process = $this->runWrapper('{"installed": []}', '', 0);

        $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        // verbose: true read from the YAML enables the "found configuration" line
        $this->assertStringContainsString('📋', $process->getErrorOutput());
    }

    public function testTemporaryFilesAreCreatedUnderTmpdirAndRemoved(): void
    {
        $process = $this->runWrapper('this is not json', '', 0);

        $this->assertSame(1, $process->getExitCode());
        $this->assertSame([], array_values(array_diff((array) scandir($this->workDir . '/tmp'), ['.', '..'])));
    }

    public function testProcessorExitsNonZeroOnInvalidJson(): void
    {
        $process = $this->runProcessor('{"installed": [}');

        $this->assertSame(1, $process->getExitCode());
        $this->assertStringContainsString('Invalid JSON', $process->getErrorOutput());
    }

    public function testProcessorExitsNonZeroWhenNoJsonObjectIsPresent(): void
    {
        $process = $this->runProcessor('');

        $this->assertSame(1, $process->getExitCode());
        $this->assertStringContainsString('no JSON object found', $process->getErrorOutput());
    }

    public function testProcessorExitsZeroWhenNothingIsOutdated(): void
    {
        $process = $this->runProcessor('{"installed": []}');

        $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        $this->assertSame('', $process->getErrorOutput());
    }

    private function runWrapper(string $stdout, string $stderr, int $exitCode): Process
    {
        $process = new Process(
            ['sh', dirname(__DIR__, 2) . '/bin/generate-composer-require.sh'],
            $this->projectDir,
            [
                'PATH'                 => $this->workDir . '/bin:' . (string) getenv('PATH'),
                'TMPDIR'               => $this->workDir . '/tmp',
                'FAKE_COMPOSER_STDOUT' => $stdout,
                'FAKE_COMPOSER_STDERR' => $stderr,
                'FAKE_COMPOSER_EXIT'   => (string) $exitCode,
            ],
            null,
            60,
        );
        $process->run();

        return $process;
    }

    private function runProcessor(string $outdatedJson): Process
    {
        $process = new Process(
            [PHP_BINARY, dirname(__DIR__, 2) . '/bin/process-updates.php'],
            $this->projectDir,
            ['OUTDATED_JSON' => $outdatedJson],
            null,
            60,
        );
        $process->run();

        return $process;
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir) || is_link($dir)) {
            @unlink($dir);

            return;
        }

        foreach (array_diff((array) scandir($dir), ['.', '..']) as $file) {
            $this->removeDirectory($dir . '/' . $file);
        }

        @rmdir($dir);
    }
}
