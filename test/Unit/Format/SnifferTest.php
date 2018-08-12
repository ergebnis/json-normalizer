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

use Localheinz\Json\Normalizer\Format\FormatInterface;
use Localheinz\Json\Normalizer\Format\Sniffer;
use Localheinz\Json\Normalizer\Format\SnifferInterface;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
final class SnifferTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsSnifferInterface(): void
    {
        $this->assertClassImplementsInterface(SnifferInterface::class, Sniffer::class);
    }

    public function testSniffRejectsInvalidJson(): void
    {
        $json = $this->faker()->realText();

        $sniffer = new Sniffer();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid JSON.',
            $json
        ));

        $sniffer->sniff($json);
    }

    /**
     * @dataProvider providerJsonAndJsonEncodeOptions
     *
     * @param int    $jsonEncodeOptions
     * @param string $json
     */
    public function testSniffReturnsFormatWithJsonEncodeOptions(int $jsonEncodeOptions, string $json): void
    {
        $sniffer = new Sniffer();

        $format = $sniffer->sniff($json);

        $this->assertInstanceOf(FormatInterface::class, $format);
        $this->assertSame($jsonEncodeOptions, $format->jsonEncodeOptions());
    }

    public function providerJsonAndJsonEncodeOptions(): array
    {
        return [
            [
                0,
                '{
  "name": "Andreas M\u00f6ller",
  "url": "https:\/\/github.com\/localheinz\/json-normalizer"
}',
            ],
            [
                \JSON_UNESCAPED_SLASHES,
                '{
  "name": "Andreas M\u00f6ller",
  "url": "https://github.com/localheinz/json-normalizer"
}',
            ],
            [
                \JSON_UNESCAPED_UNICODE,
                '{
  "name": "Andreas Möller",
  "url": "https:\/\/github.com\/localheinz\/json-normalizer"
}',
            ],
            [
                \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
                '{
  "name": "Andreas Möller",
  "url": "https://github.com/localheinz/json-normalizer"
}',
            ],
        ];
    }

    /**
     * @dataProvider providerJsonWithoutIndent
     *
     * @param string $json
     */
    public function testSniffReturnsFormatWithDefaultIndentIfJsonIsWithoutIndent(string $json): void
    {
        $sniffer = new Sniffer();

        $format = $sniffer->sniff($json);

        $this->assertInstanceOf(FormatInterface::class, $format);
        $this->assertSame('    ', $format->indent());
    }

    public function providerJsonWithoutIndent(): \Generator
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
     * @dataProvider providerPureIndentAndSniffedIndent
     * @dataProvider providerMixedIndentAndSniffedIndent
     *
     * @param string $indent
     * @param string $sniffedIndent
     */
    public function testSniffReturnsFormatWithIndentSniffedFromArray(string $indent, string $sniffedIndent): void
    {
        $json = <<<JSON
[
"foo",
${indent}"bar",
    {
        "qux": "quux"
    }
]
JSON;

        $sniffer = new Sniffer();

        $format = $sniffer->sniff($json);

        $this->assertInstanceOf(FormatInterface::class, $format);
        $this->assertSame($sniffedIndent, $format->indent());
    }

    /**
     * @dataProvider providerPureIndentAndSniffedIndent
     * @dataProvider providerMixedIndentAndSniffedIndent
     *
     * @param string $indent
     * @param string $sniffedIndent
     */
    public function testSniffReturnsFormatWithIndentSniffedFromObject(string $indent, string $sniffedIndent): void
    {
        $json = <<<JSON
{
"foo": 9000,
${indent}"bar": 123,
    "baz": {
        "qux": "quux"
    }
}
JSON;

        $sniffer = new Sniffer();

        $format = $sniffer->sniff($json);

        $this->assertInstanceOf(FormatInterface::class, $format);
        $this->assertSame($sniffedIndent, $format->indent());
    }

    public function providerPureIndentAndSniffedIndent(): \Generator
    {
        $characters = [
            'space' => ' ',
            'tab' => "\t",
        ];

        $sizes = [1, 3];

        foreach ($characters as $style => $character) {
            foreach ($sizes as $size) {
                $key = \sprintf(
                    '%s-%d',
                    $style,
                    $size
                );

                $pureIndent = \str_repeat(
                    $character,
                    $size
                );

                yield $key => [
                    $pureIndent,
                    $pureIndent,
                ];
            }
        }
    }

    public function providerMixedIndentAndSniffedIndent(): \Generator
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
     * @dataProvider providerJsonWithoutIndent
     *
     * @param string $json
     */
    public function testSniffReturnsFormatWithDefaultNewLineIfUnableToSniff(string $json): void
    {
        $sniffer = new Sniffer();

        $format = $sniffer->sniff($json);

        $this->assertInstanceOf(FormatInterface::class, $format);
        $this->assertSame(\PHP_EOL, $format->newLine());
    }

    /**
     * @dataProvider providerNewLine
     *
     * @param string $newLine
     */
    public function testSniffReturnsFormatWithNewLineSniffedFromArray(string $newLine): void
    {
        $json = <<<JSON
["foo",${newLine}"bar"]
JSON;

        $sniffer = new Sniffer();

        $format = $sniffer->sniff($json);

        $this->assertInstanceOf(FormatInterface::class, $format);
        $this->assertSame($newLine, $format->newLine());
    }

    /**
     * @dataProvider providerNewLine
     *
     * @param string $newLine
     */
    public function testSniffReturnsFormatWithNewLineNewLineSniffedFromObject(string $newLine): void
    {
        $json = <<<JSON
{"foo": 9000,${newLine}"bar": 123}
JSON;

        $sniffer = new Sniffer();

        $format = $sniffer->sniff($json);

        $this->assertInstanceOf(FormatInterface::class, $format);
        $this->assertSame($newLine, $format->newLine());
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
    public function testSniffReturnsFormatWithoutFinalNewLineIfThereIsNoFinalNewLine(string $actualWhitespace): void
    {
        $json = <<<'JSON'
{
    "foo": 9000,
    "bar": 123,
    "baz": {
        "qux": "quux"
    }
}
JSON;
        $json .= $actualWhitespace;

        $sniffer = new Sniffer();

        $format = $sniffer->sniff($json);

        $this->assertInstanceOf(FormatInterface::class, $format);
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
    public function testSniffReturnsFormatWithFinalNewLineIfThereIsAtLeastOneFinalNewLine(string $actualWhitespace): void
    {
        $json = <<<'JSON'
{
    "foo": 9000,
    "bar": 123,
    "baz": {
        "qux": "quux"
    }
}
JSON;
        $json .= $actualWhitespace;

        $sniffer = new Sniffer();

        $format = $sniffer->sniff($json);

        $this->assertInstanceOf(FormatInterface::class, $format);
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
