<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Vendor\Composer;

use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\Vendor\Composer\ConfigHashNormalizer;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\ConfigHashNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class ConfigHashNormalizerTest extends AbstractComposerTestCase
{
    public function testNormalizeDoesNotModifyOtherProperty(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON
        );

        $normalizer = new ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertSame($json->encoded(), $normalized->encoded());
    }

    /**
     * @dataProvider providerProperty
     */
    public function testNormalizeIgnoresEmptyConfigHash(string $property): void
    {
        $json = Json::fromEncoded(
            <<<JSON
{
  "{$property}": {}
}
JSON
        );

        $normalizer = new ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertSame($json->encoded(), $normalized->encoded());
    }

    /**
     * @dataProvider providerProperty
     */
    public function testNormalizeSortsConfigHashIfPropertyExists(string $property): void
    {
        $json = Json::fromEncoded(
            <<<JSON
{
  "{$property}": {
    "sort-packages": true,
    "preferred-install": "dist"
  },
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON
        );

        $expected = Json::fromEncoded(
            <<<JSON
{
  "{$property}": {
    "preferred-install": "dist",
    "sort-packages": true
  },
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON
        );

        $normalizer = new ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertSame(\json_encode(\json_decode($expected->encoded())), $normalized->encoded());
    }

    /**
     * @return \Generator<array<string>>
     */
    public function providerProperty(): \Generator
    {
        foreach (self::properties() as $value) {
            yield $value => [
                $value,
            ];
        }
    }

    /**
     * @return string[]
     */
    private static function properties(): array
    {
        return [
            'config',
            'extra',
            'scripts-descriptions',
        ];
    }
}
