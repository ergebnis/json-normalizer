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
use Localheinz\Json\Normalizer\Format\FormatSniffer;
use Localheinz\Json\Normalizer\Format\FormatSnifferInterface;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

final class FormatSnifferTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsSnifferInterface()
    {
        $this->assertClassImplementsInterface(FormatSnifferInterface::class, FormatSniffer::class);
    }

    public function testSniffRejectsInvalidJson()
    {
        $json = $this->faker()->realText();

        $sniffer = new FormatSniffer();

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
    public function testSniffReturnsFormatWithJsonEncodeOptions(int $jsonEncodeOptions, string $json)
    {
        $sniffer = new FormatSniffer();

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
                JSON_UNESCAPED_SLASHES,
'{
  "name": "Andreas M\u00f6ller",
  "url": "https://github.com/localheinz/json-normalizer"
}',
            ],
            [
                JSON_UNESCAPED_UNICODE,
'{
  "name": "Andreas Möller",
  "url": "https:\/\/github.com\/localheinz\/json-normalizer"
}',
            ],
            [
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
'{
  "name": "Andreas Möller",
  "url": "https://github.com/localheinz/json-normalizer"
}',
            ],
        ];
    }

    /**
     * @dataProvider providerJsonWithoutWhitespace
     *
     * @param string $json
     */
    public function testSniffReturnsFormatWithDefaultIndentIfUnableToSniff(string $json)
    {
        $sniffer = new FormatSniffer();

        $format = $sniffer->sniff($json);

        $this->assertInstanceOf(FormatInterface::class, $format);
        $this->assertSame('    ', $format->indent());
    }

    public function providerJsonWithoutWhitespace(): \Generator
    {
        $values = [
            'array-empty' => '[]',
            'object-empty' => '{}',
            'array-without-indent' => '["foo","bar baz"]',
            'object-without-indent' => '{"foo":"bar baz","baz":[9000,123]}',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @dataProvider providerIndent
     *
     * @param string $indent
     */
    public function testSniffReturnsFormatWithIndentSniffedFromArray(string $indent)
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

        $sniffer = new FormatSniffer();

        $format = $sniffer->sniff($json);

        $this->assertInstanceOf(FormatInterface::class, $format);
        $this->assertSame($indent, $format->indent());
    }

    /**
     * @dataProvider providerIndent
     *
     * @param string $indent
     */
    public function testSniffReturnsFormatWithIndentIndentSniffedFromObject(string $indent)
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

        $sniffer = new FormatSniffer();

        $format = $sniffer->sniff($json);

        $this->assertInstanceOf(FormatInterface::class, $format);
        $this->assertSame($indent, $format->indent());
    }

    public function providerIndent(): \Generator
    {
        $characters = [
            ' ',
            "\t",
        ];

        $counts = [1, 3];

        foreach ($characters as $character) {
            foreach ($counts as $count) {
                $indent = \str_repeat($character, $count);

                yield [
                    $indent,
                ];
            }
        }
    }

    /**
     * @dataProvider providerWhitespaceWithoutNewLine
     *
     * @param string $actualWhitespace
     */
    public function testSniffReturnsFormatWithoutFinalNewLineIfThereIsNoFinalNewLine(string $actualWhitespace)
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

        $sniffer = new FormatSniffer();

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
    public function testSniffReturnsFormatWithFinalNewLineIfThereIsAtLeastOneFinalNewLine(string $actualWhitespace)
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

        $sniffer = new FormatSniffer();

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
            PHP_EOL,
        ];

        foreach ($characters as $before) {
            foreach ($characters as $after) {
                $whitespace = $before . PHP_EOL . $after;

                yield [
                    $whitespace,
                ];
            }
        }
    }
}
