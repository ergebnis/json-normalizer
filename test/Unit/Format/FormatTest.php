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
use Localheinz\Json\Normalizer\Format\NewLineInterface;
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
        $newLine = $this->prophesize(NewLineInterface::class);
        $hasFinalNewLine = true;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid options for json_encode().',
            $jsonEncodeOptions
        ));

        new Format(
            $jsonEncodeOptions,
            $indent->reveal(),
            $newLine->reveal(),
            $hasFinalNewLine
        );
    }

    /**
     * @dataProvider providerHasFinalNewLine
     *
     * @param string $newLine
     * @param bool   $hasFinalNewLine
     */
    public function testConstructorSetsValues(bool $hasFinalNewLine): void
    {
        $jsonEncodeOptions = \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES;
        $indent = $this->prophesize(IndentInterface::class);
        $newLine = $this->prophesize(NewLineInterface::class);

        $format = new Format(
            $jsonEncodeOptions,
            $indent->reveal(),
            $newLine->reveal(),
            $hasFinalNewLine
        );

        $this->assertSame($jsonEncodeOptions, $format->jsonEncodeOptions());
        $this->assertSame($indent->reveal(), $format->indent());
        $this->assertSame($newLine->reveal(), $format->newLine());
        $this->assertSame($hasFinalNewLine, $format->hasFinalNewLine());
    }

    public function testWithJsonEncodeOptionsRejectsInvalidJsonEncodeOptions(): void
    {
        $jsonEncodeOptions = -1;

        $format = new Format(
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            $this->prophesize(IndentInterface::class)->reveal(),
            $this->prophesize(NewLineInterface::class)->reveal(),
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
            $this->prophesize(NewLineInterface::class)->reveal(),
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
            $this->prophesize(NewLineInterface::class)->reveal(),
            true
        );

        $mutated = $format->withIndent($indent->reveal());

        $this->assertInstanceOf(FormatInterface::class, $mutated);
        $this->assertNotSame($format, $mutated);
        $this->assertSame($indent->reveal(), $mutated->indent());
    }

    public function testWithNewLineClonesFormatAndSetsNewLine(): void
    {
        $newLine = $this->prophesize(NewLineInterface::class);

        $format = new Format(
            \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            $this->prophesize(IndentInterface::class)->reveal(),
            $this->prophesize(NewLineInterface::class)->reveal(),
            true
        );

        $mutated = $format->withNewLine($newLine->reveal());

        $this->assertInstanceOf(FormatInterface::class, $mutated);
        $this->assertNotSame($format, $mutated);
        $this->assertSame($newLine->reveal(), $mutated->newLine());
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
            $this->prophesize(NewLineInterface::class)->reveal(),
            false
        );

        $mutated = $format->withHasFinalNewLine($hasFinalNewLine);

        $this->assertInstanceOf(FormatInterface::class, $mutated);
        $this->assertNotSame($format, $mutated);
        $this->assertSame($hasFinalNewLine, $mutated->hasFinalNewLine());
    }

    public function providerHasFinalNewLine(): \Generator
    {
        $hasFinalNewLines = [
            'yes' => true,
            'no' => false,
        ];

        foreach ($hasFinalNewLines as $key => $hasFinalNewLine) {
            yield $key => [
                $hasFinalNewLine,
            ];
        }
    }
}
