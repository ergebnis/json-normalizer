<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Format;

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Format\Indent
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidIndentSizeException
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidIndentStringException
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidIndentStyleException
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class IndentTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testConstants(): void
    {
        $characters = [
            'space' => ' ',
            'tab' => "\t",
        ];

        self::assertSame($characters, Format\Indent::CHARACTERS);
    }

    /**
     * @dataProvider provideInvalidSize
     */
    public function testFromSizeAndStyleRejectsInvalidSize(int $size): void
    {
        $style = self::faker()->randomElement(\array_keys(Format\Indent::CHARACTERS));

        $this->expectException(Exception\InvalidIndentSizeException::class);

        Format\Indent::fromSizeAndStyle(
            $size,
            $style,
        );
    }

    /**
     * @return \Generator<array<int>>
     */
    public function provideInvalidSize(): \Generator
    {
        $sizes = [
            'int-zero' => 0,
            'int-minus-one' => -1,
            'int-less-than-minus-one' => -1 * self::faker()->numberBetween(2),
        ];

        foreach ($sizes as $key => $size) {
            yield $key => [
                $size,
            ];
        }
    }

    public function testFromSizeAndStyleRejectsInvalidStyle(): void
    {
        $faker = self::faker();

        $size = $faker->numberBetween(1);
        $style = $faker->sentence();

        $this->expectException(Exception\InvalidIndentStyleException::class);

        Format\Indent::fromSizeAndStyle(
            $size,
            $style,
        );
    }

    /**
     * @dataProvider provideSizeStyleAndIndentString
     */
    public function testFromSizeAndStyleReturnsIndent(int $size, string $style, string $string): void
    {
        $indent = Format\Indent::fromSizeAndStyle(
            $size,
            $style,
        );

        self::assertSame($string, $indent->toString());
    }

    /**
     * @return \Generator<array{0: int, 1: string, 2: string}>
     */
    public function provideSizeStyleAndIndentString(): \Generator
    {
        foreach (self::sizes() as $key => $size) {
            foreach (Format\Indent::CHARACTERS as $style => $character) {
                $string = \str_repeat(
                    $character,
                    $size,
                );

                yield [
                    $size,
                    $style,
                    $string,
                ];
            }
        }
    }

    /**
     * @dataProvider provideInvalidIndentString
     */
    public function testFromStringRejectsInvalidIndentString(string $string): void
    {
        $this->expectException(Exception\InvalidIndentStringException::class);

        Format\Indent::fromString($string);
    }

    /**
     * @return \Generator<array<string>>
     */
    public function provideInvalidIndentString(): \Generator
    {
        $strings = [
            'string-not-whitespace' => self::faker()->sentence(),
            'string-contains-line-feed' => " \n ",
            'string-mixed-space-and-tab' => " \t",
        ];

        foreach ($strings as $key => $string) {
            yield $key => [
                $string,
            ];
        }
    }

    /**
     * @dataProvider provideValidIndentString
     */
    public function testFromStringReturnsIndent(string $string): void
    {
        $indent = Format\Indent::fromString($string);

        self::assertSame($string, $indent->toString());
    }

    /**
     * @return \Generator<array<string>>
     */
    public function provideValidIndentString(): \Generator
    {
        foreach (self::sizes() as $key => $size) {
            foreach (Format\Indent::CHARACTERS as $style => $character) {
                $string = \str_repeat(
                    $character,
                    $size,
                );

                yield [
                    $string,
                ];
            }
        }
    }

    /**
     * @dataProvider provideMixedIndentAndSniffedIndent
     * @dataProvider providePureIndentAndSniffedIndent
     */
    public function testFromJsonReturnsIndentSniffedFromArray(string $actualIndent, string $sniffedIndent): void
    {
        $json = Json::fromEncoded(
            <<<JSON
[
"foo",
{$actualIndent}"bar",
    {
        "qux": "quux"
    }
]
JSON
        );

        $indent = Format\Indent::fromJson($json);

        self::assertSame($sniffedIndent, $indent->toString());
    }

    /**
     * @dataProvider provideMixedIndentAndSniffedIndent
     * @dataProvider providePureIndentAndSniffedIndent
     */
    public function testFromJsonReturnsIndentSniffedFromObject(string $actualIndent, string $sniffedIndent): void
    {
        $json = Json::fromEncoded(
            <<<JSON
{
"foo": 9000,
{$actualIndent}"bar": 123,
    "baz": {
        "qux": "quux"
    }
}
JSON
        );

        $indent = Format\Indent::fromJson($json);

        self::assertSame($sniffedIndent, $indent->toString());
    }

    /**
     * @return \Generator<array<string>>
     */
    public function providePureIndentAndSniffedIndent(): \Generator
    {
        $sizes = [
            1,
            3,
        ];

        foreach (Format\Indent::CHARACTERS as $style => $character) {
            foreach ($sizes as $size) {
                $key = \sprintf(
                    '%s-%d',
                    $style,
                    $size,
                );

                $pureIndent = \str_repeat(
                    $character,
                    $size,
                );

                yield $key => [
                    $pureIndent,
                    $pureIndent,
                ];
            }
        }
    }

    /**
     * @return \Generator<array<string>>
     */
    public function provideMixedIndentAndSniffedIndent(): \Generator
    {
        $mixedIndents = [
            'space-and-tab' => [
                " \t",
                ' ',
            ],
            'tab-and-space' => [
                "\t ",
                "\t",
            ],
        ];

        foreach ($mixedIndents as $key => [$mixedIndent, $sniffedIndent]) {
            yield $key => [
                $mixedIndent,
                $sniffedIndent,
            ];
        }
    }

    public function testFromJsonReturnsIndentWithDefaultsWhenIndentCouldNotBeSniffed(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{"foo":9000,"bar":123,"baz":{"qux":"quux"}}
JSON
        );

        $indent = Format\Indent::fromJson($json);

        $default = \str_repeat(
            ' ',
            4,
        );

        self::assertSame($default, $indent->toString());
    }

    /**
     * @return int[]
     */
    private static function sizes(): array
    {
        return [
            'int-one' => 1,
            'int-greater-than-one' => self::faker()->numberBetween(2, 5),
        ];
    }

    /**
     * @return string[]
     */
    private static function characters(): array
    {
        return [
            'space' => ' ',
            'tab' => "\t",
        ];
    }
}
