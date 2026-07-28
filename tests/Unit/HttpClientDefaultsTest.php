<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper\Tests\Unit;

use HttpClientDefaults;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function dirname;
use function sprintf;

/**
 * @internal
 */
#[CoversClass(HttpClientDefaults::class)]
final class HttpClientDefaultsTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/bin/lib/HttpClientDefaults.php';
    }

    public function testStreamContextAppliesConfiguredTimeoutAgainstHangingSocket(): void
    {
        $server = @stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
        if ($server === false) {
            self::markTestSkipped(sprintf('Cannot bind local socket: %s (%d)', $errstr, $errno));
        }

        $name = stream_socket_get_name($server, false);
        if ($name === false || !preg_match('/:(\d+)$/', $name, $m)) {
            fclose($server);
            self::markTestSkipped('Cannot resolve bound port');
        }
        $port = (int) $m[1];

        // Do not accept connections — client must hit the HTTP timeout.
        $context = HttpClientDefaults::streamContext();
        $opts    = stream_context_get_options($context);
        self::assertSame(HttpClientDefaults::TIMEOUT_SECONDS, $opts['http']['timeout']);
        self::assertSame(HttpClientDefaults::USER_AGENT, $opts['http']['user_agent']);

        // Use 1s for this test only (same shape as production defaults).
        $short = stream_context_create([
            'http' => [
                'timeout'    => 1,
                'user_agent' => HttpClientDefaults::USER_AGENT,
            ],
        ]);

        $start   = microtime(true);
        $result  = @file_get_contents(sprintf('http://127.0.0.1:%d/', $port), false, $short);
        $elapsed = microtime(true) - $start;
        fclose($server);

        self::assertFalse($result);
        self::assertGreaterThanOrEqual(0.9, $elapsed);
        self::assertLessThan(3.0, $elapsed);
    }
}
