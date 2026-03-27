<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src'])
    ->withSkip([
        __DIR__ . '/demo',
        __DIR__ . '/vendor',
    ])
    ->withPhpVersion(PhpVersion::PHP_81)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
    );
