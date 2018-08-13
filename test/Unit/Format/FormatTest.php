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
use Localheinz\Json\Normalizer\Format\IndentInterface;
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
        $indent = $this->prophesize(IndentInterface::class);
        $newLine = \PHP_EOL;
        $hasFinalNewLine = true;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid options for json_encode().',
            $jsonEncodeOptions
        ));

        new Format(
            $jsonEncodeOptions,
            $indent->reveal(),
            $newLine,
            $hasFinalNewLine
        );
    }

    /**
     * @dataProvider providerInvalidNewLine
     *
     * @param string $newLine
     */
    public function testConstructorRejectsInvalidNewLine(string $newLine): void
    {
        $jsonEncodeOptions = 0;
        $indent = $this->prophesize(IndentInterface::class);
        $hasFinalNewLine = true;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not a valid new-line character sequence.',
            $newLine
        ));

        new Format(
            $jsonEncodeOptions,
            $indent->reveal(),
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
     * @dataProvider providerNewLineAndFinalNewLine
     *
     * @param string $newLine
     * @param bool   $hasFinalNewLine
     */
    public function testConstructorSetsValues(string $newLine, bool $hasFinalNewLine): void
    {
        $jsonEncodeOptions = \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES;
        $indent = $this->prophesize(IndentInterface::class);

        $format = new Format(
            $jsonEncodeOptions,
            $indent->reveal(),
            $newLine,
            $hasFinalNewLine
        );

        $this->assertSame($jsonEncodeOptions, $format->jsonEncodeOptions());
        $this->assertSame($indent->reveal(), $format->indent());
        $this->assertSame($newLine, $format->newLine());
        $this->assertSame($hasFinalNewLine, $format->hasFinalNewLine());
    }

    public function providerNewLineAndFinalNewLine(): \Generator
    {
        foreach ($this->newLines() as $newLine) {
            foreach ($this->hasFinalNewLines() as $hasFinalNewLine) {
                yield [
                    $newLine,
                    $hasFinalNewLine,
                ];
            }
        }
    }

    public function testWithJsonEncodeOptionsRejectsInvalidJsonEncodeOptions(): void
    {
        $jsonEncodeOptions = -1;

        $format = new Format(
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            $this->prophesize(IndentInterface::class)->reveal(),
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
            $this->prophesize(IndentInterface::class)->reveal(),
            \PHP_EOL,
            true
        );

        $jsonEncodeOptions = 9000;

        $mutated = $format->withJsonEncodeOptions($jsonEncodeOptions);

        $this->assertInstanceOf(FormatInterface::class, $mutated);
        $this->assertNotSame($format, $mutated);
        $this->assertSame($jsonEncodeOptions, $mutated->jsonEncodeOptions());
    }

    public function testWithIndentClonesFormatAndSetsIndent(): void
    {
        $indent = $this->prophesize(IndentInterface::class);

        $format = new Format(
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            $this->prophesize(IndentInterface::class)->reveal(),
            \PHP_EOL,
            true
        );

        $mutated = $format->withIndent($indent->reveal());

        $this->assertInstanceOf(FormatInterface::class, $mutated);
        $this->assertNotSame($format, $mutated);
        $this->assertSame($indent->reveal(), $mutated->indent());
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
            $this->prophesize(IndentInterface::class)->reveal(),
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
            $this->prophesize(IndentInterface::class)->reveal(),
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
            $this->prophesize(IndentInterface::class)->reveal(),
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
