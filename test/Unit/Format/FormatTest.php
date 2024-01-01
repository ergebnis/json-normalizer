<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Format;

use Ergebnis\DataProvider;
use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Format;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Format\Format::class)]
#[Framework\Attributes\UsesClass(Format\Indent::class)]
#[Framework\Attributes\UsesClass(Format\JsonEncodeOptions::class)]
#[Framework\Attributes\UsesClass(Format\NewLine::class)]
final class FormatTest extends Framework\TestCase
{
    #[Framework\Attributes\DataProviderExternal(DataProvider\BoolProvider::class, 'arbitrary')]
    public function testCreateReturnsFormat(bool $hasFinalNewLine): void
    {
        $jsonEncodeOptions = Format\JsonEncodeOptions::fromInt(\JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
        $indent = Format\Indent::fromString('  ');
        $newLine = Format\NewLine::fromString("\r\n");

        $format = Format\Format::create(
            $jsonEncodeOptions,
            $indent,
            $newLine,
            $hasFinalNewLine,
        );

        self::assertSame($jsonEncodeOptions, $format->jsonEncodeOptions());
        self::assertSame($indent, $format->indent());
        self::assertSame($newLine, $format->newLine());
        self::assertSame($hasFinalNewLine, $format->hasFinalNewLine());
    }

    public function testWithJsonEncodeOptionsClonesFormatAndSetsJsonEncodeOptions(): void
    {
        $format = Format\Format::create(
            Format\JsonEncodeOptions::fromInt(\JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES),
            Format\Indent::fromString('  '),
            Format\NewLine::fromString("\r\n"),
            true,
        );

        $jsonEncodeOptions = Format\JsonEncodeOptions::fromInt(9000);

        $mutated = $format->withJsonEncodeOptions($jsonEncodeOptions);

        self::assertNotSame($format, $mutated);
        self::assertSame($jsonEncodeOptions, $mutated->jsonEncodeOptions());
    }

    public function testWithIndentClonesFormatAndSetsIndent(): void
    {
        $indent = Format\Indent::fromString("\t");

        $format = Format\Format::create(
            Format\JsonEncodeOptions::fromInt(\JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES),
            Format\Indent::fromString('  '),
            Format\NewLine::fromString("\r\n"),
            true,
        );

        $mutated = $format->withIndent($indent);

        self::assertNotSame($format, $mutated);
        self::assertSame($indent, $mutated->indent());
    }

    public function testWithNewLineClonesFormatAndSetsNewLine(): void
    {
        $newLine = Format\NewLine::fromString("\r\n");

        $format = Format\Format::create(
            Format\JsonEncodeOptions::fromInt(\JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES),
            Format\Indent::fromString('  '),
            Format\NewLine::fromString("\r"),
            true,
        );

        $mutated = $format->withNewLine($newLine);

        self::assertNotSame($format, $mutated);
        self::assertSame($newLine, $mutated->newLine());
    }

    #[Framework\Attributes\DataProviderExternal(DataProvider\BoolProvider::class, 'arbitrary')]
    public function testWithHasFinalNewLineClonesFormatAndSetsFinalNewLine(bool $hasFinalNewLine): void
    {
        $format = Format\Format::create(
            Format\JsonEncodeOptions::fromInt(\JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES),
            Format\Indent::fromString('  '),
            Format\NewLine::fromString("\r\n"),
            false,
        );

        $mutated = $format->withHasFinalNewLine($hasFinalNewLine);

        self::assertNotSame($format, $mutated);
        self::assertSame($hasFinalNewLine, $mutated->hasFinalNewLine());
    }

    #[Framework\Attributes\DataProvider('provideEncodedWithoutIndent')]
    public function testFromJsonReturnsFormatWithDefaultIndentIfJsonIsWithoutIndent(string $encoded): void
    {
        $json = Json::fromString($encoded);

        $format = Format\Format::fromJson($json);

        self::assertSame('    ', $format->indent()->toString());
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideEncodedWithoutIndent(): \Generator
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

    #[Framework\Attributes\DataProvider('provideWhitespaceWithoutNewLine')]
    public function testFromFormatReturnsFormatWithoutFinalNewLineIfThereIsNoFinalNewLine(string $actualWhitespace): void
    {
        $json = Json::fromString(
            <<<JSON
{
    "foo": 9000,
    "bar": 123,
    "baz": {
        "qux": "quux"
    }
}{$actualWhitespace}
JSON
        );

        $format = Format\Format::fromJson($json);

        self::assertFalse($format->hasFinalNewLine());
    }

    /**
     * @return \Generator<int, array{0: string}>
     */
    public static function provideWhitespaceWithoutNewLine(): \Generator
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

    #[Framework\Attributes\DataProvider('provideWhitespaceWithNewLine')]
    public function testFromFormatReturnsFormatWithFinalNewLineIfThereIsAtLeastOneFinalNewLine(string $actualWhitespace): void
    {
        $json = Json::fromString(
            <<<JSON
{
    "foo": 9000,
    "bar": 123,
    "baz": {
        "qux": "quux"
    }
}{$actualWhitespace}
JSON
        );

        $format = Format\Format::fromJson($json);

        self::assertTrue($format->hasFinalNewLine());
    }

    /**
     * @return \Generator<int, array{0: string}>
     */
    public static function provideWhitespaceWithNewLine(): \Generator
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
