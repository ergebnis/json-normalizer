<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Vendor\Composer;

use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\NormalizerInterface;
use Ergebnis\Json\Normalizer\Test;

/**
 * @internal
 */
abstract class AbstractComposerTestCase extends Test\Unit\AbstractNormalizerTestCase
{
    /**
     * @dataProvider provideJsonNotDecodingToObject
     */
    final public function testNormalizeDoesNotModifyWhenJsonDecodedIsNotAnObject(string $encoded): void
    {
        $json = Json::fromEncoded($encoded);

        /** @var class-string $className */
        $className = static::className();

        $reflection = new \ReflectionClass($className);

        /** @var NormalizerInterface $normalizer */
        $normalizer = $reflection->newInstanceWithoutConstructor();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($json->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<array<string>>
     */
    final public function provideJsonNotDecodingToObject(): \Generator
    {
        $faker = self::faker();

        $values = [
            'array' => $faker->words(),
            'bool-false' => false,
            'bool-true' => true,
            'float' => $faker->randomFloat(),
            'int' => $faker->randomNumber(),
            'null' => null,
            'string' => $faker->sentence(),
        ];

        foreach ($values as $key => $value) {
            $encoded = \json_encode($value);

            if (!\is_string($encoded)) {
                throw new \RuntimeException('Failed encoding a value to JSON.');
            }

            yield $key => [
                $encoded,
            ];
        }
    }
}
