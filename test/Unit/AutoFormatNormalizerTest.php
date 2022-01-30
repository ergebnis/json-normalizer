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

use Ergebnis\Json\Normalizer\AutoFormatNormalizer;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\Normalizer;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\AutoFormatNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Format\Format
 * @uses \Ergebnis\Json\Normalizer\Format\Indent
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\Format\NewLine
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class AutoFormatNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeNormalizesAndFormatsUsingJsonFormat(): void
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

        $formatted = Json::fromEncoded(
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
                self::equalTo(Format\Format::fromJson($json)),
            )
            ->willReturn($formatted);

        $normalizer = new AutoFormatNormalizer(
            $composedNormalizer,
            $formatter,
        );

        self::assertSame($formatted, $normalizer->normalize($json));
    }
}
