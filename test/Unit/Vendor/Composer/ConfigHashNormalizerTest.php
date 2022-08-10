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

namespace Ergebnis\Json\Normalizer\Test\Unit\Vendor\Composer;

use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\Vendor;

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

        $normalizer = new Vendor\Composer\ConfigHashNormalizer();

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

        $normalizer = new Vendor\Composer\ConfigHashNormalizer();

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

        $normalizer = new Vendor\Composer\ConfigHashNormalizer();

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

        $normalizer = new Vendor\Composer\ConfigHashNormalizer();

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

        $normalizer = new Vendor\Composer\ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }

    /**
     * @see https://getcomposer.org/doc/06-config.md#allow-plugins
     */
    public function testNormalizeCorrectlySortsAllowPluginsInConfigWithoutWildcards(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "config": {
    "sort-packages": true,
    "allow-plugins": {
      "foo/bar": true,
      "bar/qux": false,
      "foo/baz": false
    }
  }
}
JSON
        );

        $expected = Json::fromEncoded(
            <<<'JSON'
{
  "config": {
    "allow-plugins": {
      "bar/qux": false,
      "foo/bar": true,
      "foo/baz": false
    },
    "sort-packages": true
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }

    /**
     * @see https://github.com/ergebnis/composer-normalize/issues/644
     * @see https://getcomposer.org/doc/06-config.md#preferred-install
     */
    public function testNormalizeCorrectlySortsPreferredInstallInConfigWithCatchAllWildcardAtEnd(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "config": {
    "sort-packages": true,
    "preferred-install": {
      "foo/package-one": "source",
      "bar/another-package": "source",
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
      "bar/another-package": "source",
      "foo/package-one": "source",
      "*": "dist"
    },
    "sort-packages": true
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }

    /**
     * @see https://github.com/ergebnis/composer-normalize/issues/644
     * @see https://getcomposer.org/doc/06-config.md#preferred-install
     */
    public function testNormalizeCorrectlySortsPreferredInstallInConfigWithCatchAllWildcardAtStart(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "config": {
    "sort-packages": true,
    "preferred-install": {
      "*": "dist",
      "foo/package-one": "source",
      "bar/another-package": "source"
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
      "bar/another-package": "source",
      "foo/package-one": "source",
      "*": "dist"
    },
    "sort-packages": true
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }

    /**
     * @see https://github.com/ergebnis/composer-normalize/issues/644
     * @see https://getcomposer.org/doc/06-config.md#preferred-install
     */
    public function testNormalizeCorrectlySortsPreferredInstallInConfigWithCatchAllWildcardInTheMiddle(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "config": {
    "sort-packages": true,
    "preferred-install": {
      "foo/package-two": "source",
      "foo/package-one": "source",
      "*": "dist",
      "bar/another-package-2": "source",
      "bar/another-package-1": "source"
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
      "bar/another-package-1": "source",
      "bar/another-package-2": "source",
      "foo/package-one": "source",
      "foo/package-two": "source",
      "*": "dist"
    },
    "sort-packages": true
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }

    /**
     * @see https://github.com/ergebnis/composer-normalize/issues/644
     * @see https://getcomposer.org/doc/06-config.md#preferred-install
     */
    public function testNormalizeCorrectlySortsPreferredInstallInConfigWithVendorWildcardAtEnd(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "config": {
    "sort-packages": true,
    "preferred-install": {
      "foo/package-two": "dist",
      "foo/package-one": "dist",
      "*": "dist",
      "foo/*": "source"
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
      "foo/package-one": "dist",
      "foo/package-two": "dist",
      "foo/*": "source",
      "*": "dist"
    },
    "sort-packages": true
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }

    public function testNormalizeCorrectlySortsPreferredInstallInConfigWhenMoreSpecificAfterWildcard(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "config": {
    "sort-packages": true,
    "preferred-install": {
      "foo/*": "source",
      "foo/package-two": "dist",
      "foo/package-one": "dist",
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
      "foo/package-one": "dist",
      "foo/package-two": "dist",
      "foo/*": "source",
      "*": "dist"
    },
    "sort-packages": true
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\ConfigHashNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }

    public function testNormalizeCorrectlySortsPreferredInstallInConfigWhenMoreSpecificWildcardAfterWildcard(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "config": {
    "sort-packages": true,
    "preferred-install": {
      "foo/something-longer-but-alphabetically-after-package-*": "source",
      "foo/*": "dist",
      "foo/package-*": "source",
      "foo/package-one": "dist",
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
      "foo/package-one": "dist",
      "foo/package-*": "source",
      "foo/something-longer-but-alphabetically-after-package-*": "source",
      "foo/*": "dist",
      "*": "dist"
    },
    "sort-packages": true
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\ConfigHashNormalizer();

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
     * @return array<int, string>
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
