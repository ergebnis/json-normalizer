<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Format;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodeOptions
 */
final class JsonEncodeOptionsTest extends Framework\TestCase
{
    use Normalizer\Test\Util\Helper;

    public function testDefaultReturnsJsonEncodeOptions(): void
    {
        $jsonEncodeOptions = Normalizer\Format\JsonEncodeOptions::default();

        $expected = \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE;

        self::assertSame($expected, $jsonEncodeOptions->toInt());
    }

    /**
     * @dataProvider provideInvalidValue
     */
    public function testFromIntRejectsInvalidValue(int $value): void
    {
        $this->expectException(Normalizer\Exception\InvalidJsonEncodeOptions::class);

        Normalizer\Format\JsonEncodeOptions::fromInt($value);
    }

    /**
     * @return \Generator<string, array{0: int}>
     */
    public static function provideInvalidValue(): iterable
    {
        $values = [
            'int-less-than-minus-one' => -1 * self::faker()->numberBetween(2),
            'int-minus-one' => -1,
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
        $jsonEncodeOptions = Normalizer\Format\JsonEncodeOptions::fromInt($value);

        self::assertSame($value, $jsonEncodeOptions->toInt());
    }

    /**
     * @return \Generator<string, array{0: int}>
     */
    public static function provideValidValue(): iterable
    {
        $values = [
            'int-greater-than-zero' => self::faker()->numberBetween(1),
            'int-zero' => 0,
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
        string $encoded
    ): void {
        $json = Json::fromString($encoded);

        $jsonEncodeOptions = Normalizer\Format\JsonEncodeOptions::fromJson($json);

        self::assertSame($value, $jsonEncodeOptions->toInt());
    }

    /**
     * @return \Iterator<int<0, max>, array{int, string}>
     */
    public static function provideJsonEncodeOptionsAndEncoded(): \Iterator
    {
        yield [
            0,
            <<<'JSON'
                    {
                      "name": "Andreas M\u00f6ller",
                      "url": "https:\/\/github.com\/localheinz\/json-normalizer"
                    }
                    JSON,
        ];
        yield [
            \JSON_UNESCAPED_SLASHES,
            <<<'JSON'
                    {
                      "name": "Andreas M\u00F6ller",
                      "url": "https://github.com/ergebnis/json-normalizer"
                    }
                    JSON,
        ];
        yield [
            \JSON_UNESCAPED_UNICODE,
            <<<'JSON'
                    {
                      "name": "Andreas Möller",
                      "url": "https:\/\/github.com\/localheinz\/json-normalizer"
                    }
                    JSON,
        ];
        yield [
            \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
            <<<'JSON'
                    {
                      "name": "Andreas Möller",
                      "url": "https://github.com/ergebnis/json-normalizer"
                    }
                    JSON,
        ];
    }
}
