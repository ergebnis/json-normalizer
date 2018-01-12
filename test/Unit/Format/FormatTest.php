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

use Localheinz\Json\Normalizer\Format\Format;
use Localheinz\Json\Normalizer\Format\FormatInterface;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

final class FormatTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsFormatInterface()
    {
        $this->assertClassImplementsInterface(FormatInterface::class, Format::class);
    }

    public function testConstructorRejectsInvalidEncodeOptions()
    {
        $jsonEncodeOptions = -1;
        $indent = '  ';
        $finalNewLine = true;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid options for json_encode().',
            $indent
        ));

        new Format(
            $jsonEncodeOptions,
            $indent,
            $finalNewLine
        );
    }

    /**
     * @dataProvider providerInvalidIndent
     *
     * @param string $indent
     */
    public function testConstructorRejectsInvalidIndent(string $indent)
    {
        $jsonEncodeOptions = 0;
        $finalNewLine = true;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not a valid indent.',
            $indent
        ));

        new Format(
            $jsonEncodeOptions,
            $indent,
            $finalNewLine
        );
    }

    public function providerInvalidIndent(): \Generator
    {
        $values = [
            'not-whitespace' => $this->faker()->sentence,
            'contains-line-feed' => " \n ",
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @dataProvider providerJsonIndentAndFinalNewLine
     *
     * @param string $indent
     * @param bool   $finalNewLine
     */
    public function testConstructorSetsValues(string $indent, bool $finalNewLine)
    {
        $jsonEncodeOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        $format = new Format(
            $jsonEncodeOptions,
            $indent,
            $finalNewLine
        );

        $this->assertSame($jsonEncodeOptions, $format->jsonEncodeOptions());
        $this->assertSame($indent, $format->indent());
        $this->assertSame($finalNewLine, $format->hasFinalNewLine());
    }

    public function providerJsonIndentAndFinalNewLine()
    {
        $indents = [
            '  ',
            "\t",
        ];

        $hasFinalNewLines = [
            true,
            false,
        ];

        foreach ($indents as $indent) {
            foreach ($hasFinalNewLines as $hasFinalNewLine) {
                yield [
                    $indent,
                    $hasFinalNewLine,
                ];
            }
        }
    }
}
