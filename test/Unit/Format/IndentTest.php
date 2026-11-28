<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Format;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Format\Indent
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidIndentSize
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidIndentString
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidIndentStyle
 */
final class IndentTest extends Framework\TestCase
{
    use Normalizer\Test\Util\Helper;

    public function testConstants(): void
    {
        $characters = [
            'space' => ' ',
            'tab' => "\t",
        ];

        self::assertSame(Normalizer\Format\Indent::CHARACTERS, $characters);
    }

    /**
     * @dataProvider provideInvalidSize
     */
    public function testFromSizeAndStyleRejectsInvalidSize(int $size): void
    {
        $style = self::faker()->randomElement(\array_keys(Normalizer\Format\Indent::CHARACTERS));

        $this->expectException(Normalizer\Exception\InvalidIndentSize::class);
        $this->expectExceptionMessage(\sprintf('Size needs to be greater than %d, but %d is not.', 0, $size));

        Normalizer\Format\Indent::fromSizeAndStyle(
            $size,
            $style,
        );
    }

    /**
     * @return \Generator<string, array{0: int}>
     */
    public static function provideInvalidSize(): iterable
    {
        $sizes = [
            'int-less-than-minus-one' => -1 * self::faker()->numberBetween(2),
            'int-minus-one' => -1,
            'int-zero' => 0,
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

        $this->expectException(Normalizer\Exception\InvalidIndentStyle::class);
        $this->expectExceptionMessage(\sprintf('Style needs to be one of "space", "tab", but "%s" is not.', $style));

        Normalizer\Format\Indent::fromSizeAndStyle(
            $size,
            $style,
        );
    }

    /**
     * @dataProvider provideSizeStyleAndIndentString
     */
    public function testFromSizeAndStyleReturnsIndent(
        int $size,
        string $style,
        string $string
    ): void {
        $indent = Normalizer\Format\Indent::fromSizeAndStyle(
            $size,
            $style,
        );

        self::assertSame($string, $indent->toString());
    }

    /**
     * @return \Generator<int, array{0: int, 1: string, 2: string}>
     */
    public static function provideSizeStyleAndIndentString(): iterable
    {
        foreach (self::sizes() as $size) {
            foreach (Normalizer\Format\Indent::CHARACTERS as $style => $character) {
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
        $this->expectException(Normalizer\Exception\InvalidIndentString::class);

        Normalizer\Format\Indent::fromString($string);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideInvalidIndentString(): iterable
    {
        $strings = [
            'string-contains-line-feed' => " \n ",
            'string-mixed-space-and-tab' => " \t",
            'string-not-whitespace' => self::faker()->sentence(),
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
        $indent = Normalizer\Format\Indent::fromString($string);

        self::assertSame($string, $indent->toString());
    }

    /**
     * @return \Generator<int, array{0: string}>
     */
    public static function provideValidIndentString(): iterable
    {
        foreach (self::sizes() as $size) {
            foreach (Normalizer\Format\Indent::CHARACTERS as $character) {
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
    public function testFromJsonReturnsIndentSniffedFromArray(
        string $actualIndent,
        string $sniffedIndent
    ): void {
        $json = Json::fromString(<<<JSON
            [
            "foo",
            {$actualIndent}"bar",
                {
                    "qux": "quux"
                }
            ]
            JSON);

        $indent = Normalizer\Format\Indent::fromJson($json);

        self::assertSame($sniffedIndent, $indent->toString());
    }

    /**
     * @dataProvider provideMixedIndentAndSniffedIndent
     * @dataProvider providePureIndentAndSniffedIndent
     */
    public function testFromJsonReturnsIndentSniffedFromObject(
        string $actualIndent,
        string $sniffedIndent
    ): void {
        $json = Json::fromString(<<<JSON
            {
            "foo": 9000,
            {$actualIndent}"bar": 123,
                "baz": {
                    "qux": "quux"
                }
            }
            JSON);

        $indent = Normalizer\Format\Indent::fromJson($json);

        self::assertSame($sniffedIndent, $indent->toString());
    }

    /**
     * @return \Generator<string, array{0: string, 1: string}>
     */
    public static function provideMixedIndentAndSniffedIndent(): iterable
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

    /**
     * @return \Generator<string, array{0: string, 1: string}>
     */
    public static function providePureIndentAndSniffedIndent(): iterable
    {
        $sizes = [
            1,
            3,
        ];

        foreach (Normalizer\Format\Indent::CHARACTERS as $style => $character) {
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

    public function testFromJsonReturnsIndentWithDefaultsWhenIndentCouldNotBeSniffed(): void
    {
        $json = Json::fromString(<<<'JSON'
            {"foo":9000,"bar":123,"baz":{"qux":"quux"}}
            JSON);

        $indent = Normalizer\Format\Indent::fromJson($json);

        $default = \str_repeat(
            ' ',
            4,
        );

        self::assertSame($default, $indent->toString());
    }

    /**
     * @return array<string, int>
     */
    private static function sizes(): array
    {
        return [
            'int-greater-than-one' => self::faker()->numberBetween(2, 5),
            'int-one' => 1,
        ];
    }
}
