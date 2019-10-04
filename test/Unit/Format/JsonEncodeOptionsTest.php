<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit\Format;

use Localheinz\Json\Normalizer\Exception;
use Localheinz\Json\Normalizer\Format\JsonEncodeOptions;
use Localheinz\Json\Normalizer\Json;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 * @coversNothing
 */
final class JsonEncodeOptionsTest extends Framework\TestCase
{
    use Helper;

    /**
     * @dataProvider providerInvalidValue
     *
     * @param int $value
     */
    public function testFromIntRejectsInvalidValue(int $value): void
    {
        $this->expectException(Exception\InvalidJsonEncodeOptionsException::class);

        JsonEncodeOptions::fromInt($value);
    }

    public function providerInvalidValue(): \Generator
    {
        $values = [
            'int-minus-one' => -1,
            'int-less-than-minus-one' => -1 * $this->faker()->numberBetween(2),
        ];

        foreach ($values as $key => $string) {
            yield $key => [
                $string,
            ];
        }
    }

    /**
     * @dataProvider providerValidValue
     *
     * @param int $value
     */
    public function testFromIntReturnsJsonEncodeOptions(int $value): void
    {
        $jsonEncodeOptions = JsonEncodeOptions::fromInt($value);

        self::assertInstanceOf(JsonEncodeOptions::class, $jsonEncodeOptions);

        self::assertSame($value, $jsonEncodeOptions->value());
    }

    public function providerValidValue(): \Generator
    {
        $values = [
            'int-zero' => 0,
            'int-greater-than-zero' => $this->faker()->numberBetween(1),
        ];

        foreach ($values as $key => $string) {
            yield $key => [
                $string,
            ];
        }
    }

    /**
     * @dataProvider providerJsonEncodeOptionsAndEncoded
     *
     * @param int    $value
     * @param string $encoded
     */
    public function testFromJsonReturnsJsonEncodeOptions(int $value, string $encoded): void
    {
        $json = Json::fromEncoded($encoded);

        $jsonEncodeOptions = JsonEncodeOptions::fromJson($json);

        self::assertInstanceOf(JsonEncodeOptions::class, $jsonEncodeOptions);
        self::assertSame($value, $jsonEncodeOptions->value());
    }

    public function providerJsonEncodeOptionsAndEncoded(): array
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
}
