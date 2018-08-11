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

/**
 * @internal
 */
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
        $newLine = \PHP_EOL;
        $hasFinalNewLine = true;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid options for json_encode().',
            $jsonEncodeOptions
        ));

        new Format(
            $jsonEncodeOptions,
            $indent,
            $newLine,
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
        $newLine = \PHP_EOL;
        $hasFinalNewLine = true;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not a valid indent.',
            $indent
        ));

        new Format(
            $jsonEncodeOptions,
            $indent,
            $newLine,
            $hasFinalNewLine
        );
    }

    public function providerInvalidIndent(): \Generator
    {
        $values = [
            'string-contains-line-feed' => " \n ",
            'string-mixed-space-and-tab' => " \t",
            'string-not-whitespace' => $this->faker()->sentence,
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @dataProvider providerInvalidNewLine
     *
     * @param string $newLine
     */
    public function testConstructorRejectsInvalidNewLine(string $newLine): void
    {
        $jsonEncodeOptions = 0;
        $indent = '    ';
        $hasFinalNewLine = true;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not a valid new-line character sequence.',
            $newLine
        ));

        new Format(
            $jsonEncodeOptions,
            $indent,
            $newLine,
            $hasFinalNewLine
        );
    }

    public function providerInvalidNewLine(): \Generator
    {
        $values = [
            "\t",
            " \r ",
            " \r\n ",
            " \n ",
            ' ',
            "\f",
            "\x0b",
            "\x85",
        ];

        foreach ($values as $value) {
            yield [
                $value,
            ];
        }
    }

    /**
     * @dataProvider providerJsonIndentNewLineAndFinalNewLine
     *
     * @param string $indent
     * @param string $newLine
     * @param bool   $hasFinalNewLine
     */
    public function testConstructorSetsValues(string $indent, string $newLine, bool $hasFinalNewLine): void
    {
        $jsonEncodeOptions = \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES;

        $format = new Format(
            $jsonEncodeOptions,
            $indent,
            $newLine,
            $hasFinalNewLine
        );

        $this->assertSame($jsonEncodeOptions, $format->jsonEncodeOptions());
        $this->assertSame($indent, $format->indent());
        $this->assertSame($newLine, $format->newLine());
        $this->assertSame($hasFinalNewLine, $format->hasFinalNewLine());
    }

    public function providerJsonIndentNewLineAndFinalNewLine(): \Generator
    {
        foreach ($this->indents() as $indent) {
            foreach ($this->newLines() as $newLine) {
                foreach ($this->hasFinalNewLines() as $hasFinalNewLine) {
                    yield [
                        $indent,
                        $newLine,
                        $hasFinalNewLine,
                    ];
                }
            }
        }
    }

    public function testWithJsonEncodeOptionsRejectsInvalidJsonEncodeOptions(): void
    {
        $jsonEncodeOptions = -1;

        $format = new Format(
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            '    ',
            \PHP_EOL,
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
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            '    ',
            \PHP_EOL,
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
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            '    ',
            \PHP_EOL,
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
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            '    ',
            \PHP_EOL,
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
     * @dataProvider providerInvalidNewLine
     *
     * @param string $newLine
     */
    public function testWithNewLineRejectsInvalidNewLine(string $newLine): void
    {
        $format = new Format(
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            '    ',
            \PHP_EOL,
            true
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not a valid new-line character sequence.',
            $newLine
        ));

        $format->withNewLine($newLine);
    }

    /**
     * @dataProvider providerNewLine
     *
     * @param string $newLine
     */
    public function testWithNewLineClonesFormatAndSetsNewLine(string $newLine): void
    {
        $format = new Format(
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            '    ',
            \PHP_EOL,
            true
        );

        $mutated = $format->withNewLine($newLine);

        $this->assertInstanceOf(FormatInterface::class, $mutated);
        $this->assertNotSame($format, $mutated);
        $this->assertSame($newLine, $mutated->newLine());
    }

    public function providerNewLine(): \Generator
    {
        foreach ($this->newLines() as $newLine) {
            yield [
                $newLine,
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
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            '    ',
            \PHP_EOL,
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
     * @return string[]
     */
    private function newLines(): array
    {
        return [
            "\r\n",
            "\n",
            "\r",
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
