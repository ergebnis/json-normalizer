<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\FixedFormatNormalizer;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\Normalizer;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\FixedFormatNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Format\Format
 * @uses \Ergebnis\Json\Normalizer\Format\Indent
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\Format\NewLine
 */
final class FixedFormatNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeNormalizesAndFormatsUsingFormat(): void
    {
        $faker = self::faker();

        $format = Format\Format::create(
            Format\JsonEncodeOptions::fromInt($faker->numberBetween(1)),
            Format\Indent::fromString("\t"),
            Format\NewLine::fromString("\r\n"),
            $faker->boolean(),
        );

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

        $formatted = Json::fromString(
            <<<'JSON'
{
    "status": "formatted"
}
JSON
        );

        $composedNormalizer = $this->createMock(Normalizer::class);

        $composedNormalizer
            ->expects(self::once())
            ->method('normalize')
            ->with(self::identicalTo($json))
            ->willReturn($normalized);

        $formatter = $this->createMock(Format\Formatter::class);

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
