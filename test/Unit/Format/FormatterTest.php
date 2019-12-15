<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Format;

use Ergebnis\Json\Normalizer\Format\Format;
use Ergebnis\Json\Normalizer\Format\Formatter;
use Ergebnis\Json\Normalizer\Format\FormatterInterface;
use Ergebnis\Json\Normalizer\Format\Indent;
use Ergebnis\Json\Normalizer\Format\JsonEncodeOptions;
use Ergebnis\Json\Normalizer\Format\NewLine;
use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Printer;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework;
use Prophecy\Argument;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Format\Formatter
 *
 * @uses \Ergebnis\Json\Normalizer\Format\Format
 * @uses \Ergebnis\Json\Normalizer\Format\Indent
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\Format\NewLine
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class FormatterTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsFormatterInterface(): void
    {
        self::assertClassImplementsInterface(FormatterInterface::class, Formatter::class);
    }

    /**
     * @dataProvider providerFinalNewLine
     *
     * @param bool $hasFinalNewLine
     */
    public function testFormatEncodesWithJsonEncodeOptionsIndentsAndPossiblySuffixesWithFinalNewLine(bool $hasFinalNewLine): void
    {
        $faker = self::faker();

        $jsonEncodeOptions = $faker->numberBetween(1);
        $indentString = \str_repeat(' ', $faker->numberBetween(1, 5));
        $newLineString = $faker->randomElement([
            "\r\n",
            "\n",
            "\r",
        ]);

        $json = Json::fromEncoded(
            <<<'JSON'
{
    "name": "Andreas M\u00f6ller",
    "url": "https:\/\/github.com\/localheinz\/json-normalizer",
    "string-apostroph": "'",
    "string-numeric": "9000",
    "string-quote": "\"",
    "string-tag": "<p>"
}
JSON
        );

        $encodedWithJsonEncodeOptions = \json_encode(
            $json->decoded(),
            $jsonEncodeOptions
        );

        $printedWithIndentAndNewLine = <<<'JSON'
{
    "status": "printed with indent and new-line"
}
JSON;

        $format = new Format(
            JsonEncodeOptions::fromInt($jsonEncodeOptions),
            Indent::fromString($indentString),
            NewLine::fromString($newLineString),
            $hasFinalNewLine
        );

        $printer = $this->prophesize(Printer\PrinterInterface::class);

        $printer
            ->print(
                Argument::is($encodedWithJsonEncodeOptions),
                Argument::is($format->indent()->__toString()),
                Argument::is($format->newLine()->__toString())
            )
            ->shouldBeCalled()
            ->willReturn($printedWithIndentAndNewLine);

        $formatter = new Formatter($printer->reveal());

        $formatted = $formatter->format(
            $json,
            $format
        );

        self::assertInstanceOf(Json::class, $formatted);

        $suffix = $hasFinalNewLine ? $newLineString : '';

        $expected = $printedWithIndentAndNewLine . $suffix;

        self::assertSame($expected, $formatted->encoded());
    }

    public function providerFinalNewLine(): \Generator
    {
        $values = [
            'bool-false' => false,
            'bool-true' => true,
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }
}
