<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Utils;

use function dirname;

/**
 * @internal
 */
#[CoversClass(Utils::class)]
final class UtilsTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/bin/lib/Utils.php';
    }

    #[DataProvider('normalizeVersionProvider')]
    public function testNormalizeVersion(?string $input, ?string $expected): void
    {
        self::assertSame($expected, Utils::normalizeVersion($input));
    }

    /**
     * @return iterable<string, array{0: ?string, 1: ?string}>
     */
    public static function normalizeVersionProvider(): iterable
    {
        yield 'null' => [null, null];
        yield 'stable' => ['8.1.0', '8.1.0'];
        yield 'stable with v prefix' => ['v8.1.0', '8.1.0'];
        yield 'dev branch without hash' => ['dev-develop', 'dev-develop'];
        yield 'dev branch short hash (Composer getFullPrettyVersion)' => ['dev-develop 97190d9', 'dev-develop'];
        yield 'dev branch full hash' => ['dev-develop 97190d9bae8a906c9bf23a60d8fe776952f38aad', 'dev-develop'];
        yield 'dev master short hash' => ['dev-master abcdef0', 'dev-master'];
        yield 'dev feature branch with slash-like name kept as token' => ['dev-fix/foo deadbeef', 'dev-fix/foo'];
        yield 'semver must not strip trailing hex-looking segment after space wrongly' => ['1.2.3', '1.2.3'];
        // Space + non-hash must stay intact (not a Composer full-pretty branch form)
        yield 'unrelated space kept' => ['1.0.0 beta1', '1.0.0 beta1'];
    }

    public function testBuildComposerCommandKeepsDevBranchAsSingleToken(): void
    {
        $command = Utils::buildComposerCommand([
            'nowo/risk:' . Utils::normalizeVersion('dev-develop 97190d9'),
            'sentry/sentry:4.32.0',
        ], false);

        self::assertSame(
            'composer require --with-all-dependencies nowo/risk:dev-develop sentry/sentry:4.32.0',
            $command,
        );
        self::assertStringNotContainsString(' 97190d9', (string) $command);
    }
}
