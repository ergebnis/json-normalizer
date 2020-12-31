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

        self::assertJsonStringEqualsJsonStringNormalized($json->encoded(), $normalized->encoded());
    }

    /**
     * @dataProvider provideProperty
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

        self::assertJsonStringEqualsJsonStringNormalized($json->encoded(), $normalized->encoded());
    }

    public function testNormalizeIgnoresEmptyConfigHashButContinuesNormalizing(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "config": {},
  "extra": {
    "foo": "bar",
    "bar": "baz"
  }
}
JSON
        );

        $expected = Json::fromEncoded(
            <<<'JSON'
{
  "config": {},
  "extra": {
    "bar": "baz",
    "foo": "bar"
  }
}
JSON
        );

        $normalizer = new ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }

    /**
     * @dataProvider provideProperty
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

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }

    /**
     * @dataProvider provideProperty
     */
    public function testNormalizeSortsConfigHashRecursivelyIfPropertyExists(string $property): void
    {
        $json = Json::fromEncoded(
            <<<JSON
{
  "{$property}": {
    "sort-packages": true,
    "preferred-install": "dist",
    "foo": {
      "qux": "quux",
      "bar": {
        "qux": "quz",
        "baz": "qux"
      }
    }
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
    "foo": {
      "bar": {
        "baz": "qux",
        "qux": "quz"
      },
      "qux": "quux"
    },
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

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }

    /**
     * @see https://github.com/ergebnis/composer-normalize/issues/644
     * @see https://getcomposer.org/doc/06-config.md#preferred-install
     */
    public function testNormalizeDoesNotSortPreferredInstallInConfig(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "config": {
    "sort-packages": true,
    "preferred-install": {
      "foo/*": "source",
      "bar/*": "source",
      "*": "dist"
    }
  }
}
JSON
        );

        $expected = Json::fromEncoded(
            <<<'JSON'
{
  "config": {
    "preferred-install": {
      "foo/*": "source",
      "bar/*": "source",
      "*": "dist"
    },
    "sort-packages": true
  }
}
JSON
        );

        $normalizer = new ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }

    public function testNormalizeSortsPreferredInstallInOtherProperty(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "extra": {
    "something": {
      "preferred-install": {
        "foo": "bar",
        "bar": "baz"
      }
    }
  }
}
JSON
        );

        $expected = Json::fromEncoded(
            <<<'JSON'
{
  "extra": {
    "something": {
      "preferred-install": {
        "bar": "baz",
        "foo": "bar"
      }
    }
  }
}
JSON
        );

        $normalizer = new ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<array<string>>
     */
    public function provideProperty(): \Generator
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
