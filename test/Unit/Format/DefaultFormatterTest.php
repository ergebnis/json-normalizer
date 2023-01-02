<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Format;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Printer;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Format\DefaultFormatter
 *
 * @uses \Ergebnis\Json\Normalizer\Format\Format
 * @uses \Ergebnis\Json\Normalizer\Format\Indent
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\Format\NewLine
 */
final class DefaultFormatterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\BoolProvider::arbitrary()
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

        $json = Json::fromString(
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
            $jsonEncodeOptions,
        );

        $printedWithIndentAndNewLine = <<<'JSON'
{
    "status": "printed with indent and new-line"
}
JSON;

        $format = Format\Format::create(
            Format\JsonEncodeOptions::fromInt($jsonEncodeOptions),
            Format\Indent::fromString($indentString),
            Format\NewLine::fromString($newLineString),
            $hasFinalNewLine,
        );

        $printer = $this->createMock(Printer\PrinterInterface::class);

        $printer
            ->expects(self::once())
            ->method('print')
            ->with(
                self::identicalTo($encodedWithJsonEncodeOptions),
                self::identicalTo($format->indent()->toString()),
                self::identicalTo($format->newLine()->toString()),
            )
            ->willReturn($printedWithIndentAndNewLine);

        $formatter = new Format\DefaultFormatter($printer);

        $formatted = $formatter->format(
            $json,
            $format,
        );

        self::assertInstanceOf(Json::class, $formatted);

        $suffix = $hasFinalNewLine ? $newLineString : '';

        $expected = $printedWithIndentAndNewLine . $suffix;

        self::assertSame($expected, $formatted->encoded());
    }
}
