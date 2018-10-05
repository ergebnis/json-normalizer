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

use Localheinz\Json\Normalizer\FixedFormatNormalizer;
use Localheinz\Json\Normalizer\Format;
use Localheinz\Json\Normalizer\JsonInterface;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Prophecy\Argument;

/**
 * @internal
 */
final class FixedFormatNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeNormalizesAndFormatsUsingFormat(): void
    {
        $faker = $this->faker();

        $json = $this->prophesize(JsonInterface::class);
        $normalized = $this->prophesize(JsonInterface::class);
        $formatted = $this->prophesize(JsonInterface::class);

        $composedNormalizer = $this->prophesize(NormalizerInterface::class);

        $composedNormalizer
            ->normalize(Argument::is($json->reveal()))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $format = new Format\Format(
            $faker->numberBetween(1),
            Format\Indent::fromString('  '),
            Format\NewLine::fromString("\r\n"),
            $faker->boolean
        );

        $formatter = $this->prophesize(Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized->reveal()),
                Argument::is($format)
            )
            ->shouldBeCalled()
            ->willReturn($formatted->reveal());

        $normalizer = new FixedFormatNormalizer(
            $composedNormalizer->reveal(),
            $format,
            $formatter->reveal()
        );

        $this->assertSame($formatted->reveal(), $normalizer->normalize($json->reveal()));
    }
}
