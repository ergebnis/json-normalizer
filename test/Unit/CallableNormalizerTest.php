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

use Localheinz\Json\Normalizer\CallableNormalizer;

/**
 * @internal
 */
final class CallableNormalizerTest extends AbstractNormalizerTestCase
{
    /**
     * @dataProvider providerCallable
     *
     * @param callable $callable
     */
    public function testNormalizePassesJsonThroughCallable(callable $callable): void
    {
        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com",
    "level": 1
}
JSON;

        $normalized = $callable($json);

        $normalizer = new CallableNormalizer($callable);

        $this->assertSame($normalized, $normalizer->normalize($json));
    }

    public function providerCallable(): \Generator
    {
        $values = [
            'closure' => function (string $json): string {
                $decoded = \json_decode($json);

                foreach (\get_object_vars($decoded) as $name => $value) {
                    if (!\is_int($value)) {
                        continue;
                    }

                    $decoded->{$name} = $value + 1;
                }

                return \json_encode($decoded);
            },
            'function-name' => 'trim',
            'method' => [
                self::class,
                'callable',
            ],
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    public static function callable(string $json): string
    {
        $decoded = \json_decode($json);

        foreach (\get_object_vars($decoded) as $name => $value) {
            if (!\is_string($value)) {
                continue;
            }

            $decoded->{$name} = $value . ' (ok)';
        }

        return \json_encode($decoded);
    }
}
