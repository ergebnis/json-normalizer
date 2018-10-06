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
use Localheinz\Json\Normalizer\Format\Indent;
use Localheinz\Json\Normalizer\Format\JsonEncodeOptions;
use Localheinz\Json\Normalizer\Format\NewLine;
use Localheinz\Json\Normalizer\Json;
use PHPUnit\Framework;

/**
 * @internal
 */
final class FormatTest extends Framework\TestCase
{
    /**
     * @dataProvider providerHasFinalNewLine
     *
     * @param bool $hasFinalNewLine
     */
    public function testConstructorSetsValues(bool $hasFinalNewLine): void
    {
        $jsonEncodeOptions = JsonEncodeOptions::fromInt(\JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
        $indent = Indent::fromString('  ');
        $newLine = NewLine::fromString("\r\n");

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

    public function testWithJsonEncodeOptionsClonesFormatAndSetsJsonEncodeOptions(): void
    {
        $format = new Format(
            JsonEncodeOptions::fromInt(\JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES),
            Indent::fromString('  '),
            NewLine::fromString("\r\n"),
            true
        );

        $jsonEncodeOptions = JsonEncodeOptions::fromInt(9000);

        $mutated = $format->withJsonEncodeOptions($jsonEncodeOptions);

        $this->assertInstanceOf(Format::class, $mutated);
        $this->assertNotSame($format, $mutated);
        $this->assertSame($jsonEncodeOptions, $mutated->jsonEncodeOptions());
    }

    public function testWithIndentClonesFormatAndSetsIndent(): void
    {
        $indent = Indent::fromString("\t");

        $format = new Format(
            JsonEncodeOptions::fromInt(\JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES),
            Indent::fromString('  '),
            NewLine::fromString("\r\n"),
            true
        );

        $mutated = $format->withIndent($indent);

        $this->assertInstanceOf(Format::class, $mutated);
        $this->assertNotSame($format, $mutated);
        $this->assertSame($indent, $mutated->indent());
    }

    public function testWithNewLineClonesFormatAndSetsNewLine(): void
    {
        $newLine = NewLine::fromString("\r\n");

        $format = new Format(
            JsonEncodeOptions::fromInt(\JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES),
            Indent::fromString('  '),
            NewLine::fromString("\r"),
            true
        );

        $mutated = $format->withNewLine($newLine);

        $this->assertInstanceOf(Format::class, $mutated);
        $this->assertNotSame($format, $mutated);
        $this->assertSame($newLine, $mutated->newLine());
    }

    /**
     * @dataProvider providerHasFinalNewLine
     *
     * @param bool $hasFinalNewLine
     */
    public function testWithHasFinalNewLineClonesFormatAndSetsFinalNewLine(bool $hasFinalNewLine): void
    {
        $format = new Format(
            JsonEncodeOptions::fromInt(\JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES),
            Indent::fromString('  '),
            NewLine::fromString("\r\n"),
            false
        );

        $mutated = $format->withHasFinalNewLine($hasFinalNewLine);

        $this->assertInstanceOf(Format::class, $mutated);
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

    /**
     * @dataProvider providerEncodedWithoutIndent
     *
     * @param string $encoded
     */
    public function testFromJsonReturnsFormatWithDefaultIndentIfJsonIsWithoutIndent(string $encoded): void
    {
        $json = Json::fromEncoded($encoded);

        $format = Format::fromJson($json);

        $this->assertInstanceOf(Format::class, $format);
        $this->assertSame('    ', $format->indent()->__toString());
    }

    public function providerEncodedWithoutIndent(): \Generator
    {
        $values = [
            'array-empty' => '[]',
            'array-without-indent' => '["foo","bar baz"]',
            'bool-false' => 'false',
            'bool-true' => 'true',
            'float' => '3.14',
            'int' => '9000',
            'null' => 'null',
            'object-empty' => '{}',
            'object-without-indent' => '{"foo":"bar baz","baz":[9000,123]}',
            'string-blank' => '" "',
            'string-word' => '"foo"',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @dataProvider providerEncodedWithoutIndent
     *
     * @param string $encoded
     */
    public function testFromJsonReturnsFormatWithDefaultNewLineIfUnableToSniff(string $encoded): void
    {
        $json = Json::fromEncoded($encoded);

        $format = Format::fromJson($json);

        $this->assertInstanceOf(Format::class, $format);
        $this->assertSame(\PHP_EOL, $format->newLine()->__toString());
    }

    /**
     * @dataProvider providerNewLine
     *
     * @param string $newLine
     */
    public function testFromFormatReturnsFormatWithNewLineSniffedFromArray(string $newLine): void
    {
        $json = Json::fromEncoded(
<<<JSON
["foo",${newLine}"bar"]
JSON
        );

        $format = Format::fromJson($json);

        $this->assertInstanceOf(Format::class, $format);
        $this->assertSame($newLine, $format->newLine()->__toString());
    }

    /**
     * @dataProvider providerNewLine
     *
     * @param string $newLine
     */
    public function testFromFormatReturnsFormatWithNewLineNewLineSniffedFromObject(string $newLine): void
    {
        $json = Json::fromEncoded(
<<<JSON
{"foo": 9000,${newLine}"bar": 123}
JSON
        );

        $format = Format::fromJson($json);

        $this->assertInstanceOf(Format::class, $format);
        $this->assertSame($newLine, $format->newLine()->__toString());
    }

    public function providerNewLine(): \Generator
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

    /**
     * @dataProvider providerWhitespaceWithoutNewLine
     *
     * @param string $actualWhitespace
     */
    public function testFromFormatReturnsFormatWithoutFinalNewLineIfThereIsNoFinalNewLine(string $actualWhitespace): void
    {
        $json = Json::fromEncoded(
<<<JSON
{
    "foo": 9000,
    "bar": 123,
    "baz": {
        "qux": "quux"
    }
}${actualWhitespace}
JSON
        );

        $format = Format::fromJson($json);

        $this->assertInstanceOf(Format::class, $format);
        $this->assertFalse($format->hasFinalNewLine());
    }

    public function providerWhitespaceWithoutNewLine(): \Generator
    {
        $characters = [
            ' ',
            "\t",
        ];

        foreach ($characters as $one) {
            foreach ($characters as $two) {
                $whitespace = $one . $two;

                yield [
                    $whitespace,
                ];
            }
        }
    }

    /**
     * @dataProvider providerWhitespaceWithNewLine
     *
     * @param string $actualWhitespace
     */
    public function testFromFormatReturnsFormatWithFinalNewLineIfThereIsAtLeastOneFinalNewLine(string $actualWhitespace): void
    {
        $json = Json::fromEncoded(
<<<JSON
{
    "foo": 9000,
    "bar": 123,
    "baz": {
        "qux": "quux"
    }
}${actualWhitespace}
JSON
        );

        $format = Format::fromJson($json);

        $this->assertInstanceOf(Format::class, $format);
        $this->assertTrue($format->hasFinalNewLine());
    }

    public function providerWhitespaceWithNewLine(): \Generator
    {
        $characters = [
            '',
            ' ',
            "\t",
            \PHP_EOL,
        ];

        foreach ($characters as $before) {
            foreach ($characters as $after) {
                $whitespace = $before . \PHP_EOL . $after;

                yield [
                    $whitespace,
                ];
            }
        }
    }
}
