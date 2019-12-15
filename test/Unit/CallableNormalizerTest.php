<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Normalizer\CallableNormalizer;
use Ergebnis\Json\Normalizer\Json;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\CallableNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class CallableNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizePassesJsonThroughCallable(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
    "status": "original"
}
JSON
        );

        $normalized = Json::fromEncoded(
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
