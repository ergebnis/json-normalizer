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

namespace Ergebnis\Json\Normalizer\Test\Unit\Format;

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Format\NewLine;
use Ergebnis\Json\Normalizer\Json;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Format\NewLine
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidNewLineStringException
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class NewLineTest extends Framework\TestCase
{
    /**
     * @dataProvider providerInvalidNewLineString
     */
    public function testFromStringRejectsInvalidNewLineString(string $string): void
    {
        $this->expectException(Exception\InvalidNewLineStringException::class);

        NewLine::fromString($string);
    }

    /**
     * @return \Generator<array<string>>
     */
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
     */
    public function testFromStringReturnsNewLine(string $string): void
    {
        $newLine = NewLine::fromString($string);

        self::assertSame($string, $newLine->__toString());
    }

    /**
     * @return \Generator<array<string>>
     */
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

        self::assertSame(\PHP_EOL, $newLine->__toString());
    }

    /**
     * @dataProvider providerNewLine
     */
    public function testFromFormatReturnsFormatWithNewLineSniffedFromArray(string $newLineString): void
    {
        $json = Json::fromEncoded(
            <<<JSON
["foo",{$newLineString}"bar"]
JSON
        );

        $newLine = NewLine::fromJson($json);

        self::assertSame($newLineString, $newLine->__toString());
    }

    /**
     * @dataProvider providerNewLine
     */
    public function testFromFormatReturnsFormatWithNewLineNewLineSniffedFromObject(string $newLineString): void
    {
        $json = Json::fromEncoded(
            <<<JSON
{"foo": 9000,{$newLineString}"bar": 123}
JSON
        );

        $newLine = NewLine::fromJson($json);

        self::assertSame($newLineString, $newLine->__toString());
    }

    /**
     * @return \Generator<array<string>>
     */
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
