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
use Ergebnis\Json\Normalizer\Vendor\Composer\VersionConstraintNormalizer;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\VersionConstraintNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class VersionConstraintNormalizerTest extends AbstractComposerTestCase
{
    /**
     * @dataProvider providerVersionConstraint
     */
    public function testNormalizeDoesNotModifyOtherProperty(string $constraint): void
    {
        $json = Json::fromEncoded(
            <<<JSON
{
  "foo": {
    "bar/baz": "{$constraint}"
  }
}
JSON
        );

        $normalizer = new VersionConstraintNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertSame($json->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<array<string>>
     */
    public function providerVersionConstraint(): \Generator
    {
        foreach (\array_keys(self::versionConstraints()) as $versionConstraint) {
            yield [
                $versionConstraint,
            ];
        }
    }

    /**
     * @dataProvider providerProperty
     */
    public function testNormalizeIgnoresEmptyPackageHash(string $property): void
    {
        $json = Json::fromEncoded(
            <<<JSON
{
  "{$property}": {}
}
JSON
        );

        $normalizer = new VersionConstraintNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertSame(\json_encode(\json_decode($json->encoded())), $normalized->encoded());
    }

    /**
     * @return \Generator<array<string>>
     */
    public function providerProperty(): \Generator
    {
        $properties = self::propertiesWhereValuesOfHashAreVersionConstraints();

        foreach ($properties as $property) {
            yield [
                $property,
            ];
        }
    }

    /**
     * @dataProvider providerPropertyAndVersionConstraint
     */
    public function testNormalizeNormalizesVersionConstraints(string $property, string $versionConstraint, string $normalizedVersionConstraint): void
    {
        $json = Json::fromEncoded(
            <<<JSON
{
  "{$property}": {
    "bar/baz": "{$versionConstraint}"
  }
}
JSON
        );

        $expected = Json::fromEncoded(
            <<<JSON
{
  "{$property}": {
    "bar/baz": "{$normalizedVersionConstraint}"
  }
}
JSON
        );

        $normalizer = new VersionConstraintNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonString($expected->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<array<string>>
     */
    public function providerPropertyAndVersionConstraint(): \Generator
    {
        $properties = self::propertiesWhereValuesOfHashAreVersionConstraints();
        $versionConstraints = self::versionConstraints();

        foreach ($properties as $property) {
            foreach ($versionConstraints as $versionConstraint => $normalizedVersionConstraint) {
                yield [
                    $property,
                    $versionConstraint,
                    $normalizedVersionConstraint,
                ];
            }
        }
    }

    /**
     * @dataProvider providerPropertyAndUntrimmedVersionConstraint
     */
    public function testNormalizeNormalizesTrimsVersionConstraints(string $property, string $versionConstraint, string $trimmedVersionConstraint): void
    {
        $json = Json::fromEncoded(
            <<<JSON
{
  "{$property}": {
    "bar/baz": "{$versionConstraint}"
  }
}
JSON
        );

        $expected = Json::fromEncoded(
            <<<JSON
{
  "{$property}": {
    "bar/baz": "{$trimmedVersionConstraint}"
  }
}
JSON
        );

        $normalizer = new VersionConstraintNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonString($expected->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<array<string>>
     */
    public function providerPropertyAndUntrimmedVersionConstraint(): \Generator
    {
        $spaces = [
            '',
            ' ',
        ];

        $properties = self::propertiesWhereValuesOfHashAreVersionConstraints();
        $versionConstraints = \array_unique(\array_values(self::versionConstraints()));

        foreach ($properties as $property) {
            foreach ($versionConstraints as $trimmedVersionConstraint) {
                foreach ($spaces as $prefix) {
                    foreach ($spaces as $suffix) {
                        $untrimmedVersionConstraint = $prefix . $trimmedVersionConstraint . $suffix;

                        if ($trimmedVersionConstraint === $untrimmedVersionConstraint) {
                            continue;
                        }

                        yield [
                            $property,
                            $untrimmedVersionConstraint,
                            $trimmedVersionConstraint,
                        ];
                    }
                }
            }
        }
    }

    /**
     * @return string[]
     */
    private static function propertiesWhereValuesOfHashAreVersionConstraints(): array
    {
        return [
            'conflict',
            'provide',
            'replace',
            'require',
            'require-dev',
        ];
    }

    /**
     * @see https://getcomposer.org/doc/articles/versions.md
     *
     * @return string[]
     */
    private static function versionConstraints(): array
    {
        return [
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#branches
             */
            'dev-main' => 'dev-main',
            'dev-my-feature' => 'dev-my-feature',
            'dev-main#bf2eeff' => 'dev-main#bf2eeff',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#exact-version-constraint
             */
            '1.0.2' => '1.0.2',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#version-range
             */
            '>=1.0' => '>=1.0',
            '>=1.0 <2.0' => '>=1.0 <2.0',
            '>=1.0,<2.0' => '>=1.0,<2.0',
            '>=1.0  <2.0' => '>=1.0 <2.0',
            '>=1.0 , <2.0' => '>=1.0,<2.0',
            '>=1.0 <1.1 || >=1.2' => '>=1.0 <1.1 || >=1.2',
            '>=1.0,<1.1 || >=1.2' => '>=1.0,<1.1 || >=1.2',
            '>=1.0  <1.1||>=1.2' => '>=1.0 <1.1 || >=1.2',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#hyphenated-version-range-
             */
            '1.0 - 2.0' => '1.0 - 2.0',
            '1.0  -  2.0' => '1.0 - 2.0',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#next-significant-release-operators
             */
            '~1.2' => '~1.2',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#caret-version-range-
             */
            '^1.2.3' => '^1.2.3',
        ];
    }
}
