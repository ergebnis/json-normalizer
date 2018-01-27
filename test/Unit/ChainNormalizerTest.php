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
use Localheinz\Json\Normalizer\NormalizerInterface;
use Prophecy\Argument;

final class ChainNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizePassesJsonThroughNormalizers(): void
    {
        $count = $this->faker()->numberBetween(3, 5);

        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com",
    "normalized-by": "%s"
}
JSON;

        $normalize = function (string $json, int $index) {
            return \sprintf(
                $json,
                $index
            );
        };

        $normalized = $normalize(
            $json,
            $count - 1
        );

        $normalizers = \array_fill(0, $count, null);

        $normalizers = \array_map(function (int $index) use ($json, $normalize) {
            $normalized = $normalize(
                $json,
                $index
            );

            if (0 < $index) {
                $json = $normalize(
                    $json,
                    $index - 1
                );
            }

            $normalizer = $this->prophesize(NormalizerInterface::class);

            $normalizer
                ->normalize(Argument::exact($json))
                ->shouldBeCalled()
                ->willReturn($normalized);

            return $normalizer->reveal();
        }, \array_keys($normalizers));

        $normalizer = new ChainNormalizer(...$normalizers);

        $this->assertSame($normalized, $normalizer->normalize($json));
    }
}
