<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\CallableNormalizer;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\CallableNormalizer
 */
final class CallableNormalizerTest extends Framework\TestCase
{
    public function testNormalizePassesJsonThroughCallable(): void
    {
        $json = Json::fromString(
            <<<'JSON'
{
    "status": "original"
}
JSON
        );

        $normalized = Json::fromString(
            <<<'JSON'
{
    "status": "normalized"
}
JSON
        );

        $callable = static function () use ($normalized): Json {
            return $normalized;
        };

        $normalizer = new CallableNormalizer($callable);

        self::assertSame($normalized, $normalizer->normalize($json));
    }
}
