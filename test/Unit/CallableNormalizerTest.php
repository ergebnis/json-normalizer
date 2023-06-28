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
use Ergebnis\Json\Normalizer\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(CallableNormalizer::class)]
final class CallableNormalizerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testNormalizePassesJsonThroughCallable(): void
    {
        $json = Json::fromString(
            <<<'JSON'
{
    "status": "original"
}
JSON
        );

        $expected = Json::fromString(
            <<<'JSON'
{
    "status": "normalized"
}
JSON
        );

        $callable = static function () use ($expected): Json {
            return $expected;
        };

        $normalizer = new CallableNormalizer($callable);

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringIdenticalToJsonString($expected->encoded(), $normalized->encoded());
    }
}
