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
use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Format;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Format\NewLine::class)]
#[Framework\Attributes\UsesClass(Exception\InvalidNewLineString::class)]
final class NewLineTest extends Framework\TestCase
{
    #[Framework\Attributes\DataProvider('provideInvalidNewLineString')]
    public function testFromStringRejectsInvalidNewLineString(string $string): void
    {
        $this->expectException(Exception\InvalidNewLineString::class);
        $this->expectExceptionMessage(\sprintf('"%s" is not a valid new-line character sequence.', $string));

        Format\NewLine::fromString($string);
    }

    /**
     * @return \Generator<int, array{0: string}>
     */
    public static function provideInvalidNewLineString(): \Generator
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

    #[Framework\Attributes\DataProvider('provideValidNewLineString')]
    public function testFromStringReturnsNewLine(string $string): void
    {
        $newLine = Format\NewLine::fromString($string);

        self::assertSame($string, $newLine->toString());
    }

    /**
     * @return \Generator<int, array{0: string}>
     */
    public static function provideValidNewLineString(): \Generator
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

        $json = Json::fromString($encoded);

        $newLine = Format\NewLine::fromJson($json);

        self::assertSame(\PHP_EOL, $newLine->toString());
    }

    #[Framework\Attributes\DataProvider('provideNewLine')]
    public function testFromFormatReturnsFormatWithNewLineSniffedFromArray(string $newLineString): void
    {
        $json = Json::fromString(
            <<<JSON
["foo",{$newLineString}"bar"]
JSON
        );

        $newLine = Format\NewLine::fromJson($json);

        self::assertSame($newLineString, $newLine->toString());
    }

    #[Framework\Attributes\DataProvider('provideNewLine')]
    public function testFromFormatReturnsFormatWithNewLineNewLineSniffedFromObject(string $newLineString): void
    {
        $json = Json::fromString(
            <<<JSON
{"foo": 9000,{$newLineString}"bar": 123}
JSON
        );

        $newLine = Format\NewLine::fromJson($json);

        self::assertSame($newLineString, $newLine->toString());
    }

    /**
     * @return \Generator<int, array{0: string}>
     */
    public static function provideNewLine(): \Generator
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
