<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2020 Andreas Möller
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
use Ergebnis\Json\Normalizer\NormalizerInterface;
use Prophecy\Argument;

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

        $composedNormalizer = $this->prophesize(NormalizerInterface::class);

        $composedNormalizer
            ->normalize(Argument::is($json))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $formatter = $this->prophesize(Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($json->format())
            )
            ->shouldBeCalled()
            ->willReturn($formatted);

        $normalizer = new AutoFormatNormalizer(
            $composedNormalizer->reveal(),
            $formatter->reveal()
        );

        self::assertSame($formatted, $normalizer->normalize($json));
    }
}
