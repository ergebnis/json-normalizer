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

use Localheinz\Json\Normalizer\Format\NewLine;
use Localheinz\Json\Normalizer\Format\NewLineInterface;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
final class NewLineTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsNewLineInterface(): void
    {
        $this->assertClassImplementsInterface(NewLineInterface::class, NewLine::class);
    }

    /**
     * @dataProvider providerInvalidNewLineString
     *
     * @param string $string
     */
    public function testFromStringRejectsInvalidNewLineString(string $string): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not a valid new-line character sequence.',
            $string
        ));

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

        $this->assertInstanceOf(NewLineInterface::class, $newLine);
        $this->assertSame($string, $newLine->__toString());
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
}
