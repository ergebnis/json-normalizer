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

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Json
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidJsonEncoded
 */
final class JsonTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromEncodedRejectsInvalidEncoded(): void
    {
        $string = self::faker()->realText();

        $this->expectException(Exception\InvalidJsonEncoded::class);

        Json::fromEncoded($string);
    }

    /**
     * @dataProvider provideEncoded
     */
    public function testFromEncodedReturnsJson(string $encoded): void
    {
        $json = Json::fromEncoded($encoded);

        self::assertSame($encoded, $json->toString());
        self::assertSame($encoded, $json->encoded());
        self::assertSame($encoded, \json_encode($json->decoded()));
    }

    /**
     * @return \Generator<array<null|array|bool|float|int|string>>
     */
    public function provideEncoded(): \Generator
    {
        $values = [
            'array-indexed' => [
                'foo',
                'bar',
            ],
            'array-associative' => [
                'foo' => 'bar',
            ],
            'bool-false' => false,
            'bool-true' => true,
            'float' => 3.14,
            'int' => 9000,
            'null' => null,
            'string' => 'foo',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                \json_encode($value),
            ];
        }
    }
}
