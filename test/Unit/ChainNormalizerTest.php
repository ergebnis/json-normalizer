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

use Localheinz\Json\Normalizer\ChainNormalizer;
use Localheinz\Json\Normalizer\JsonInterface;
use Localheinz\Json\Normalizer\NormalizerInterface;

/**
 * @internal
 */
final class ChainNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizePassesJsonThroughNormalizers(): void
    {
        $json = $this->prophesize(JsonInterface::class);

        $results = \array_map(function () {
            return $this->prophesize(JsonInterface::class)->reveal();
        }, \range(0, 4));

        $last = \end($results);

        $normalizers = \array_map(function ($result) use ($json) {
            static $previous = null;

            if (null === $previous) {
                $previous = $json;
            }

            $normalizer = $this->prophesize(NormalizerInterface::class);

            $normalizer
                ->normalize($previous)
                ->shouldBeCalled()
                ->willReturn($result);

            $previous = $result;

            return $normalizer->reveal();
        }, $results);

        $normalizer = new ChainNormalizer(...$normalizers);

        $normalized = $normalizer->normalize($json->reveal());

        $this->assertSame($last, $normalized);
    }
}
