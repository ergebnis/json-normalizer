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

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Vendor;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\VersionConstraintNormalizer
 */
final class VersionConstraintNormalizerTest extends AbstractComposerTestCase
{
    /**
     * @dataProvider provideVersionConstraint
     */
    public function testNormalizeDoesNotModifyOtherProperty(string $constraint): void
    {
        $json = Json::fromString(
            <<<JSON
{
  "foo": {
    "bar/baz": "{$constraint}"
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\VersionConstraintNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($json->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<array<string>>
     */
    public static function provideVersionConstraint(): \Generator
    {
        foreach (\array_keys(self::versionConstraints()) as $versionConstraint) {
            yield [
                $versionConstraint,
            ];
        }
    }

    /**
     * @dataProvider provideProperty
     */
    public function testNormalizeIgnoresEmptyPackageHash(string $property): void
    {
        $json = Json::fromString(
            <<<JSON
{
  "{$property}": {}
}
JSON
        );

        $normalizer = new Vendor\Composer\VersionConstraintNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($json->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<array<string>>
     */
    public static function provideProperty(): \Generator
    {
        $properties = self::propertiesWhereValuesOfHashAreVersionConstraints();

        foreach ($properties as $property) {
            yield [
                $property,
            ];
        }
    }

    /**
     * @dataProvider providePropertyAndVersionConstraint
     */
    public function testNormalizeNormalizesVersionConstraints(
        string $property,
        string $versionConstraint,
        string $normalizedVersionConstraint,
    ): void {
        $json = Json::fromString(
            <<<JSON
{
  "{$property}": {
    "bar/baz": "{$versionConstraint}"
  }
}
JSON
        );

        $expected = Json::fromString(
            <<<JSON
{
  "{$property}": {
    "bar/baz": "{$normalizedVersionConstraint}"
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\VersionConstraintNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonString($expected->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<array<string>>
     */
    public static function providePropertyAndVersionConstraint(): \Generator
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
     * @dataProvider providePropertyAndUntrimmedVersionConstraint
     */
    public function testNormalizeNormalizesTrimsVersionConstraints(
        string $property,
        string $versionConstraint,
        string $trimmedVersionConstraint,
    ): void {
        $json = Json::fromString(
            <<<JSON
{
  "{$property}": {
    "bar/baz": "{$versionConstraint}"
  }
}
JSON
        );

        $expected = Json::fromString(
            <<<JSON
{
  "{$property}": {
    "bar/baz": "{$trimmedVersionConstraint}"
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\VersionConstraintNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonString($expected->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<array<string>>
     */
    public static function providePropertyAndUntrimmedVersionConstraint(): \Generator
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
     * @return array<int, string>
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
     * @return array<string, string>
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
            '2.x-dev' => '2.x-dev',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#exact-version-constraint
             */
            '1.0.2' => '1.0.2',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#version-range
             */
            '>=1.0' => '>=1.0',
            '>=1.0 <2.0' => '>=1.0 <2.0',
            '>=1.0,<2.0' => '>=1.0 <2.0',
            '>=1.0  <2.0' => '>=1.0 <2.0',
            '>=1.0 , <2.0' => '>=1.0 <2.0',
            '>=1.0 <1.1 || >=1.2' => '>=1.0 <1.1 || >=1.2',
            '>=1.0,<1.1 || >=1.2' => '>=1.0 <1.1 || >=1.2',
            '>=1.0  <1.1||>=1.2' => '>=1.0 <1.1 || >=1.2',
            '<2.0 >=1.0' => '>=1.0 <2.0',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#hyphenated-version-range-
             */
            '1.0 - 2.0' => '1.0 - 2.0',
            '1.0  -  2.0' => '1.0 - 2.0',
            '3.0 - 4.0 || 1.0 - 2.0' => '1.0 - 2.0 || 3.0 - 4.0',
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#wildcard-version-range-
             */
            '1.2.*' => '~1.2.0', // prefer tilde operator when equivalent
            '3.*' => '^3.0', // prefer caret operator when equivalent
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#next-significant-release-operators
             */
            '~1.2' => '^1.2', // prefer caret operator when equivalent
            '~1.2.3' => '~1.2.3',
            '~1.2.3 || ~1.2.5' => '~1.2.3', // remove overlapping / duplicate
            '~2.4.6 || ~1.3.5' => '~1.3.5 || ~2.4.6', // sort
            '~5' => '^5.0', // minimum number of parts to version string, prefer caret when equivalent
            /**
             * @see https://getcomposer.org/doc/articles/versions.md#caret-version-range-
             */
            '^1.2.3' => '^1.2.3',
            '^4.5' => '^4.5',
            '^7.0 || ^7.1 || ^7.2 || ^8.0' => '^7.0 || ^8.0', // remove overlapping / duplicate
            '^6.5 || ^7.0 || ^7.1 || ^7.2 || ^8.0' => '^6.5 || ^7.0 || ^8.0', // remove overlapping / duplicate
            '^1.3 || ~1.5.7' => '^1.3', // remove overlapping / duplicate
            '^2.4 || ^1.2' => '^1.2 || ^2.4', // sort
            '^1' => '^1.0', // minimum number of parts to version string
            '^2.3.1|^3.0' => '^2.3.1 || ^3.0', // single to double pipes
            '^4.0 || 1.2.1' => '1.2.1 || ^4.0',
            '^4.0 || 5.2.1' => '^4.0 || 5.2.1',
            '^4.0.2 || ~4.0.3' => '^4.0.2',
        ];
    }
}
