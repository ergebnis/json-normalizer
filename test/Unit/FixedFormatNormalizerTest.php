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
use Localheinz\Json\Normalizer\NormalizerInterface;
use Localheinz\Json\Printer;
use Prophecy\Argument;

final class FixedFormatNormalizerTest extends AbstractNormalizerTestCase
{
    /**
     * @dataProvider providerFinalNewLine
     *
     * @param bool   $hasFinalNewLine
     * @param string $suffix
     */
    public function testNormalizeEncodesWithJsonEncodeOptionsIndentsAndPossiblySuffixesWithFinalNewLine(bool $hasFinalNewLine, string $suffix): void
    {
        $faker = $this->faker();

        $jsonEncodeOptions = $faker->numberBetween(1);
        $indent = \str_repeat(' ', $faker->numberBetween(1, 5));

        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $normalized = <<<'JSON'
{
    "name": "Andreas Möller (normalized)",
    "url": "https://localheinz.com"
}
JSON;

        $encoded = \json_encode(
            \json_decode($normalized),
            $jsonEncodeOptions
        );

        $printed = <<<'JSON'
{
    "name": "Andreas Möller (printed)",
    "url": "https://localheinz.com"
}
JSON;

        $composedNormalizer = $this->prophesize(NormalizerInterface::class);

        $composedNormalizer
            ->normalize(Argument::is($json))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $format = $this->prophesize(Format\FormatInterface::class);

        $format
            ->jsonEncodeOptions()
            ->shouldBeCalled()
            ->willReturn($jsonEncodeOptions);

        $format
            ->indent()
            ->shouldBeCalled()
            ->willReturn($indent);

        $format
            ->hasFinalNewLine()
            ->shouldBeCalled()
            ->willReturn($hasFinalNewLine);

        $printer = $this->prophesize(Printer\PrinterInterface::class);

        $printer
            ->print(
                Argument::is($encoded),
                Argument::is($indent)
            )
            ->shouldBeCalled()
            ->willReturn($printed);

        $normalizer = new FixedFormatNormalizer(
            $composedNormalizer->reveal(),
            $format->reveal(),
            $printer->reveal()
        );

        $this->assertSame($printed . $suffix, $normalizer->normalize($json));
    }

    public function providerFinalNewLine(): \Generator
    {
        $values = [
            'without-final-new-line' => [
                false,
                '',
            ],
            'with-final-new-line' => [
                true,
                PHP_EOL,
            ],
        ];

        foreach ($values as $key => [$hasFinalNewLine, $suffix]) {
            yield $key => [
                $hasFinalNewLine,
                $suffix,
            ];
        }
    }
}
