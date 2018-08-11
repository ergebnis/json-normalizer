<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit;

use Localheinz\Json\Normalizer\CallableNormalizer;
use Localheinz\Json\Normalizer\JsonInterface;

/**
 * @internal
 */
final class CallableNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizePassesJsonThroughCallable(): void
    {
        $json = $this->prophesize(JsonInterface::class);
        $normalized = $this->prophesize(JsonInterface::class);

        $callable = function () use ($normalized): JsonInterface {
            return $normalized->reveal();
        };

        $normalizer = new CallableNormalizer($callable);

        $this->assertSame($normalized->reveal(), $normalizer->normalize($json->reveal()));
    }
}
