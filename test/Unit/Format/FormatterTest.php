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

namespace Localheinz\Json\Normalizer\Test\Unit\Format;

use Localheinz\Json\Normalizer\Format\FormatInterface;
use Localheinz\Json\Normalizer\Format\Formatter;
use Localheinz\Json\Normalizer\Format\FormatterInterface;
use Localheinz\Json\Printer;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;
use Prophecy\Argument;

final class FormatterTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsFormatterInterface()
    {
        $this->assertClassImplementsInterface(FormatterInterface::class, Formatter::class);
    }

    public function testFormatRejectsInvalidJson(): void
    {
        $json = $this->faker()->realText();

        $formatter = new Formatter($this->prophesize(Printer\PrinterInterface::class)->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid JSON.',
            $json
        ));

        $formatter->format(
            $json,
            $this->prophesize(FormatInterface::class)->reveal()
        );
    }

    /**
     * @dataProvider providerFinalNewLine
     *
     * @param bool $hasFinalNewLine
     */
    public function testFormatEncodesWithJsonEncodeOptionsIndentsAndPossiblySuffixesWithFinalNewLine(bool $hasFinalNewLine): void
    {
        $faker = $this->faker();

        $jsonEncodeOptions = $faker->numberBetween(1);
        $indent = \str_repeat(' ', $faker->numberBetween(1, 5));
        $newLine = $faker->randomElement([
            "\r\n",
            "\n",
            "\r",
        ]);

        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $encoded = \json_encode(
            \json_decode($json),
            $jsonEncodeOptions
        );

        $printed = <<<'JSON'
{
    "name": "Andreas Möller (printed)",
    "url": "https://localheinz.com"
}
JSON;

        $format = $this->prophesize(FormatInterface::class);

        $format
            ->jsonEncodeOptions()
            ->shouldBeCalled()
            ->willReturn($jsonEncodeOptions);

        $format
            ->indent()
            ->shouldBeCalled()
            ->willReturn($indent);

        $format
            ->newLine()
            ->shouldBeCalled()
            ->willReturn($newLine);

        $format
            ->hasFinalNewLine()
            ->shouldBeCalled()
            ->willReturn($hasFinalNewLine);

        $printer = $this->prophesize(Printer\PrinterInterface::class);

        $printer
            ->print(
                Argument::is($encoded),
                Argument::is($indent),
                Argument::is($newLine)
            )
            ->shouldBeCalled()
            ->willReturn($printed);

        $formatter = new Formatter($printer->reveal());

        $formatted = $formatter->format(
            $json,
            $format->reveal()
        );

        $suffix = $hasFinalNewLine ? $newLine : '';

        $this->assertSame($printed . $suffix, $formatted);
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
