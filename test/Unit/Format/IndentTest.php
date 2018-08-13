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

use Localheinz\Json\Normalizer\Format\Indent;
use Localheinz\Json\Normalizer\Format\IndentInterface;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
final class IndentTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsIndentInterface(): void
    {
        $this->assertClassImplementsInterface(IndentInterface::class, Indent::class);
    }

    /**
     * @dataProvider providerInvalidSize
     *
     * @param int $size
     */
    public function testFromSizeAndStyleRejectsInvalidSize(int $size): void
    {
        $style = $this->faker()->randomElement([
            'space',
            'tab',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Size needs to be greater than 0, but %d is not.',
            $size
        ));

        Indent::fromSizeAndStyle(
            $size,
            $style
        );
    }

    public function providerInvalidSize(): \Generator
    {
        $sizes = [
            'int-zero' => 0,
            'int-minus-one' => -1,
            'int-less-than-minus-one' => -1 * $this->faker()->numberBetween(2),
        ];

        foreach ($sizes as $key => $size) {
            yield $key => [
                $size,
            ];
        }
    }

    public function testFromSizeAndStyleRejectsInvalidStyle(): void
    {
        $faker = $this->faker();

        $size = $faker->numberBetween(1);
        $style = $faker->sentence;

        $styles = [
            'space',
            'tab',
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Style needs to be one of "%s", but "%s" is not.',
            \implode('", "', $styles),
            $style
        ));

        Indent::fromSizeAndStyle(
            $size,
            $style
        );
    }

    /**
     * @dataProvider providerSizeStyleAndIndentString
     *
     * @param int    $size
     * @param string $style
     * @param string $string
     */
    public function testFromSizeAndStyleReturnsIndent(int $size, string $style, string $string): void
    {
        $indent = Indent::fromSizeAndStyle(
            $size,
            $style
        );

        $this->assertInstanceOf(IndentInterface::class, $indent);

        $this->assertSame($string, $indent->__toString());
    }

    public function providerSizeStyleAndIndentString(): \Generator
    {
        $sizes = [
            'int-one' => 1,
            'int-greater-than-one' => $this->faker()->numberBetween(2, 5),
        ];

        $characters = [
            'space' => ' ',
            'tab' => "\t",
        ];

        foreach ($sizes as $key => $size) {
            foreach ($characters as $style => $character) {
                $string = \str_repeat(
                    $character,
                    $size
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
     * @dataProvider providerInvalidIndentString
     *
     * @param string $string
     */
    public function testFromStringRejectsInvalidIndentString(string $string): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not a valid indent.',
            $string
        ));

        Indent::fromString($string);
    }

    public function providerInvalidIndentString(): \Generator
    {
        $strings = [
            'string-not-whitespace' => $this->faker()->sentence,
            'string-contains-line-feed' => " \n ",
            'string-mixed-space-and-tab' => " \t",
        ];

        foreach ($strings as $key => $string) {
            yield $key => [
                $string,
            ];
        }
    }
}
