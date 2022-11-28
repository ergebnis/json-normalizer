<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Format;

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class JsonEncodeOptionsTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider provideInvalidValue
     */
    public function testFromIntRejectsInvalidValue(int $value): void
    {
        $this->expectException(Exception\InvalidJsonEncodeOptions::class);

        Format\JsonEncodeOptions::fromInt($value);
    }

    /**
     * @return \Generator<array<int>>
     */
    public function provideInvalidValue(): \Generator
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
     * @return \Generator<array<int>>
     */
    public function provideValidValue(): \Generator
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
        $json = Json::fromEncoded($encoded);

        $jsonEncodeOptions = Format\JsonEncodeOptions::fromJson($json);

        self::assertSame($value, $jsonEncodeOptions->toInt());
    }

    /**
     * @return array<array{0: int, 1: string}>
     */
    public function provideJsonEncodeOptionsAndEncoded(): array
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
  "url": "https://github.com/ergebnis/json-normalizer"
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
  "url": "https://github.com/ergebnis/json-normalizer"
}',
            ],
        ];
    }
}
