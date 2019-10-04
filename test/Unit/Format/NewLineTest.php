<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit\Format;

use Localheinz\Json\Normalizer\Exception;
use Localheinz\Json\Normalizer\Format\NewLine;
use Localheinz\Json\Normalizer\Json;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Localheinz\Json\Normalizer\Format\NewLine
 */
final class NewLineTest extends Framework\TestCase
{
    /**
     * @dataProvider providerInvalidNewLineString
     *
     * @param string $string
     */
    public function testFromStringRejectsInvalidNewLineString(string $string): void
    {
        $this->expectException(Exception\InvalidNewLineStringException::class);

        NewLine::fromString($string);
    }

    public function providerInvalidNewLineString(): \Generator
    {
        $strings = [
            "\t",
            " \r ",
            " \r\n ",
            " \n ",
            ' ',
            "\f",
            "\x0b",
            "\x85",
        ];

        foreach ($strings as $string) {
            yield [
                $string,
            ];
        }
    }

    /**
     * @dataProvider providerValidNewLineString
     *
     * @param string $string
     */
    public function testFromStringReturnsNewLine(string $string): void
    {
        $newLine = NewLine::fromString($string);

        self::assertInstanceOf(NewLine::class, $newLine);
        self::assertSame($string, $newLine->__toString());
    }

    public function providerValidNewLineString(): \Generator
    {
        $strings = [
            "\n",
            "\r",
            "\r\n",
        ];

        foreach ($strings as $string) {
            yield [
                $string,
            ];
        }
    }

    public function testFromJsonReturnsFormatWithDefaultNewLineIfNoneFound(): void
    {
        $encoded = '{"foo": "bar"}';

        $json = Json::fromEncoded($encoded);

        $newLine = NewLine::fromJson($json);

        self::assertInstanceOf(NewLine::class, $newLine);
        self::assertSame(\PHP_EOL, $newLine->__toString());
    }

    /**
     * @dataProvider providerNewLine
     *
     * @param string $newLineString
     */
    public function testFromFormatReturnsFormatWithNewLineSniffedFromArray(string $newLineString): void
    {
        $json = Json::fromEncoded(
            <<<JSON
["foo",{$newLineString}"bar"]
JSON
        );

        $newLine = NewLine::fromJson($json);

        self::assertInstanceOf(NewLine::class, $newLine);
        self::assertSame($newLineString, $newLine->__toString());
    }

    /**
     * @dataProvider providerNewLine
     *
     * @param string $newLineString
     */
    public function testFromFormatReturnsFormatWithNewLineNewLineSniffedFromObject(string $newLineString): void
    {
        $json = Json::fromEncoded(
            <<<JSON
{"foo": 9000,{$newLineString}"bar": 123}
JSON
        );

        $newLine = NewLine::fromJson($json);

        self::assertInstanceOf(NewLine::class, $newLine);
        self::assertSame($newLineString, $newLine->__toString());
    }

    public function providerNewLine(): \Generator
    {
        $values = [
            "\r\n",
            "\n",
            "\r",
        ];

        foreach ($values as $newLine) {
            yield [
                $newLine,
            ];
        }
    }
}
