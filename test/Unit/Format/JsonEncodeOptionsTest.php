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

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodeOptions
 */
final class JsonEncodeOptionsTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testDefaultReturnsJsonEncodeOptions(): void
    {
        $jsonEncodeOptions = Format\JsonEncodeOptions::default();

        $expected = \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE;

        self::assertSame($expected, $jsonEncodeOptions->toInt());
    }

    /**
     * @dataProvider provideInvalidValue
     */
    public function testFromIntRejectsInvalidValue(int $value): void
    {
        $this->expectException(Exception\InvalidJsonEncodeOptions::class);

        Format\JsonEncodeOptions::fromInt($value);
    }

    /**
     * @return \Generator<string, array{0: int}>
     */
    public static function provideInvalidValue(): iterable
    {
        $values = [
            'int-minus-one' => -1,
            'int-less-than-minus-one' => -1 * self::faker()->numberBetween(2),
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @dataProvider provideValidValue
     */
    public function testFromIntReturnsJsonEncodeOptions(int $value): void
    {
        $jsonEncodeOptions = Format\JsonEncodeOptions::fromInt($value);

        self::assertSame($value, $jsonEncodeOptions->toInt());
    }

    /**
     * @return \Generator<string, array{0: int}>
     */
    public static function provideValidValue(): iterable
    {
        $values = [
            'int-zero' => 0,
            'int-greater-than-zero' => self::faker()->numberBetween(1),
        ];

        foreach ($values as $key => $string) {
            yield $key => [
                $string,
            ];
        }
    }

    /**
     * @dataProvider provideJsonEncodeOptionsAndEncoded
     */
    public function testFromJsonReturnsJsonEncodeOptions(
        int $value,
        string $encoded,
    ): void {
        $json = Json::fromString($encoded);

        $jsonEncodeOptions = Format\JsonEncodeOptions::fromJson($json);

        self::assertSame($value, $jsonEncodeOptions->toInt());
    }

    /**
     * @return list<array{0: int, 1: string}>
     */
    public static function provideJsonEncodeOptionsAndEncoded(): iterable
    {
        yield from [
            [
                0,
                <<<'JSON'
{
  "name": "Andreas M\u00f6ller",
  "url": "https:\/\/github.com\/localheinz\/json-normalizer"
}
JSON,
            ],
            [
                \JSON_UNESCAPED_SLASHES,
                <<<'JSON'
{
  "name": "Andreas M\u00F6ller",
  "url": "https://github.com/ergebnis/json-normalizer"
}
JSON,
            ],
            [
                \JSON_UNESCAPED_UNICODE,
                <<<'JSON'
{
  "name": "Andreas Möller",
  "url": "https:\/\/github.com\/localheinz\/json-normalizer"
}
JSON,
            ],
            [
                \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
                <<<'JSON'
{
  "name": "Andreas Möller",
  "url": "https://github.com/ergebnis/json-normalizer"
}
JSON,
            ],
        ];
    }
}
