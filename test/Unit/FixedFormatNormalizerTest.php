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
use Localheinz\Json\Normalizer\Json;
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

        $format = new Format\Format(
            Format\JsonEncodeOptions::fromInt($faker->numberBetween(1)),
            Format\Indent::fromString("\t"),
            Format\NewLine::fromString("\r\n"),
            $faker->boolean
        );

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

        $formatted = Json::fromEncoded(
<<<'JSON'
{
    "status": "formatted"
}
JSON
        );

        $composedNormalizer = $this->prophesize(NormalizerInterface::class);

        $composedNormalizer
            ->normalize(Argument::is($json))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $formatter = $this->prophesize(Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($format)
            )
            ->shouldBeCalled()
            ->willReturn($formatted);

        $normalizer = new FixedFormatNormalizer(
            $composedNormalizer->reveal(),
            $format,
            $formatter->reveal()
        );

        self::assertSame($formatted, $normalizer->normalize($json));
    }
}
