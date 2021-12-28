<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Normalizer\FixedFormatNormalizer;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\NormalizerInterface;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\FixedFormatNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Format\Format
 * @uses \Ergebnis\Json\Normalizer\Format\Indent
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\Format\NewLine
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class FixedFormatNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeNormalizesAndFormatsUsingFormat(): void
    {
        $faker = self::faker();

        $format = new Format\Format(
            Format\JsonEncodeOptions::fromInt($faker->numberBetween(1)),
            Format\Indent::fromString("\t"),
            Format\NewLine::fromString("\r\n"),
            $faker->boolean,
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

        $composedNormalizer = $this->createMock(NormalizerInterface::class);

        $composedNormalizer
            ->expects(self::once())
            ->method('normalize')
            ->with(self::identicalTo($json))
            ->willReturn($normalized);

        $formatter = $this->createMock(Format\FormatterInterface::class);

        $formatter
            ->expects(self::once())
            ->method('format')
            ->with(
                self::identicalTo($normalized),
                self::identicalTo($format),
            )
            ->willReturn($formatted);

        $normalizer = new FixedFormatNormalizer(
            $composedNormalizer,
            $format,
            $formatter,
        );

        self::assertSame($formatted, $normalizer->normalize($json));
    }
}
