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

namespace Localheinz\Json\Normalizer\Test\Unit;

use Localheinz\Json\Normalizer\Json;
use Localheinz\Json\Normalizer\JsonInterface;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
final class JsonTest extends Framework\TestCase
{
    use Helper;

    public function testFromEncodedRejectsInvalidJsonString(): void
    {
        $string = $this->faker()->realText();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid JSON.',
            $string
        ));

        Json::fromEncoded($string);
    }

    /**
     * @dataProvider providerEncoded
     *
     * @param string $encoded
     */
    public function testFromEncodedReturnsJson(string $encoded): void
    {
        $json = Json::fromEncoded($encoded);

        $this->assertInstanceOf(JsonInterface::class, $json);
        $this->assertSame($encoded, $json->__toString());
        $this->assertSame($encoded, $json->encoded());
        $this->assertSame($encoded, \json_encode($json->decoded()));
    }

    public function providerEncoded(): \Generator
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
