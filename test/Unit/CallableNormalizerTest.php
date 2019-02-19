<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit;

use Localheinz\Json\Normalizer\CallableNormalizer;
use Localheinz\Json\Normalizer\Json;

/**
 * @internal
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
