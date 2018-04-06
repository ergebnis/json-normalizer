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

    public function testImplementsFormatInterface(): void
    {
        $this->assertClassImplementsInterface(FormatInterface::class, Format::class);
    }

    public function testConstructorRejectsInvalidEncodeOptions(): void
    {
        $jsonEncodeOptions = -1;
        $indent = '  ';
        $hasFinalNewLine = true;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid options for json_encode().',
            $jsonEncodeOptions
        ));

        new Format(
            $jsonEncodeOptions,
            $indent,
            $hasFinalNewLine
        );
    }

    /**
     * @dataProvider providerInvalidIndent
     *
     * @param string $indent
     */
    public function testConstructorRejectsInvalidIndent(string $indent): void
    {
        $jsonEncodeOptions = 0;
        $hasFinalNewLine = true;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not a valid indent.',
            $indent
        ));

        new Format(
            $jsonEncodeOptions,
            $indent,
            $hasFinalNewLine
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
     * @param bool   $hasFinalNewLine
     */
    public function testConstructorSetsValues(string $indent, bool $hasFinalNewLine): void
    {
        $jsonEncodeOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        $format = new Format(
            $jsonEncodeOptions,
            $indent,
            $hasFinalNewLine
        );

        $this->assertSame($jsonEncodeOptions, $format->jsonEncodeOptions());
        $this->assertSame($indent, $format->indent());
        $this->assertSame($hasFinalNewLine, $format->hasFinalNewLine());
    }

    public function providerJsonIndentAndFinalNewLine(): \Generator
    {
        foreach ($this->indents() as $indent) {
            foreach ($this->hasFinalNewLines() as $hasFinalNewLine) {
                yield [
                    $indent,
                    $hasFinalNewLine,
                ];
            }
        }
    }

    public function testWithJsonEncodeOptionsRejectsInvalidJsonEncodeOptions(): void
    {
        $jsonEncodeOptions = -1;

        $format = new Format(
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            '    ',
            true
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid options for json_encode().',
            $jsonEncodeOptions
        ));

        $format->withJsonEncodeOptions($jsonEncodeOptions);
    }

    public function testWithJsonEncodeOptionsClonesFormatAndSetsJsonEncodeOptions(): void
    {
        $format = new Format(
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            '    ',
            true
        );

        $jsonEncodeOptions = 9000;

        $mutated = $format->withJsonEncodeOptions($jsonEncodeOptions);

        $this->assertInstanceOf(FormatInterface::class, $mutated);
        $this->assertNotSame($format, $mutated);
        $this->assertSame($jsonEncodeOptions, $mutated->jsonEncodeOptions());
    }

    /**
     * @dataProvider providerInvalidIndent
     *
     * @param string $indent
     */
    public function testWithIndentRejectsInvalidIndent(string $indent): void
    {
        $format = new Format(
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            '    ',
            true
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not a valid indent.',
            $indent
        ));

        $format->withIndent($indent);
    }

    /**
     * @dataProvider providerIndent
     *
     * @param string $indent
     */
    public function testWithIndentClonesFormatAndSetsIndent(string $indent): void
    {
        $format = new Format(
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            '    ',
            true
        );

        $mutated = $format->withIndent($indent);

        $this->assertInstanceOf(FormatInterface::class, $mutated);
        $this->assertNotSame($format, $mutated);
        $this->assertSame($indent, $mutated->indent());
    }

    public function providerIndent(): \Generator
    {
        foreach ($this->indents() as $key => $indent) {
            yield $key => [
                $indent,
            ];
        }
    }

    /**
     * @dataProvider providerHasFinalNewLine
     *
     * @param bool $hasFinalNewLine
     */
    public function testWithHasFinalNewLineClonesFormatAndSetsFinalNewLine(bool $hasFinalNewLine): void
    {
        $format = new Format(
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            '    ',
            false
        );

        $mutated = $format->withHasFinalNewLine($hasFinalNewLine);

        $this->assertInstanceOf(FormatInterface::class, $mutated);
        $this->assertNotSame($format, $mutated);
        $this->assertSame($hasFinalNewLine, $mutated->hasFinalNewLine());
    }

    public function providerHasFinalNewLine(): \Generator
    {
        foreach ($this->hasFinalNewLines() as $key => $hasFinalNewLine) {
            yield $key => [
                $hasFinalNewLine,
            ];
        }
    }

    /**
     * @return string[]
     */
    private function indents(): array
    {
        return [
            'space' => '  ',
            'tab' => "\t",
        ];
    }

    /**
     * @return bool[]
     */
    private function hasFinalNewLines(): array
    {
        return [
            'yes' => true,
            'no' => false,
        ];
    }
}
