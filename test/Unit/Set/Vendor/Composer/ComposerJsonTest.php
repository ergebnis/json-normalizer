<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Set\Vendor\Composer;

use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Set;
use Ergebnis\Json\Normalizer\Skip;
use Ergebnis\Json\Pointer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Set\Vendor\Composer\ComposerJson
 *
 * @uses \Ergebnis\Json\Normalizer\Rule\Name
 * @uses \Ergebnis\Json\Normalizer\Rule\Prune\EmptyOptionalProperties
 * @uses \Ergebnis\Json\Normalizer\Rule\Sort\PropertiesByName
 * @uses \Ergebnis\Json\Normalizer\Rule\Sort\PropertiesBySchema
 * @uses \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Bin\SortElements
 * @uses \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Config\SortProperties
 * @uses \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Config\SortPropertiesWithWildcards
 * @uses \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Packages\MergeDuplicateExtensions
 * @uses \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Packages\SortProperties
 * @uses \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Repositories\SortFilterElements
 * @uses \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\Constraints
 * @uses \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\NormalizeSeparators
 * @uses \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\RemoveExtraSpaces
 * @uses \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\RemoveLeadingV
 * @uses \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\Trim
 * @uses \Ergebnis\Json\Normalizer\Set\Name
 * @uses \Ergebnis\Json\Normalizer\Skip
 */
final class ComposerJsonTest extends Framework\TestCase
{
    public function testNameReturnsName(): void
    {
        $set = Set\Vendor\Composer\ComposerJson::create();

        self::assertSame('@composer-json', $set->name()->toString());
    }

    public function testRulesReturnsRulesInOrder(): void
    {
        $set = Set\Vendor\Composer\ComposerJson::create();

        $names = \array_map(static function (Rule $rule): string {
            return $rule->name()->toString();
        }, $set->rules());

        $expected = [
            'sort/properties-by-schema',
            'sort/properties-by-name',
            'prune/empty-optional-properties',
            'vendor/composer/bin/sort-elements',
            'vendor/composer/config/sort-properties',
            'vendor/composer/config/sort-properties-with-wildcards',
            'vendor/composer/repositories/sort-filter-elements',
            'vendor/composer/packages/merge-duplicate-extensions',
            'vendor/composer/packages/sort-properties',
            'vendor/composer/version-constraint/trim',
            'vendor/composer/version-constraint/remove-extra-spaces',
            'vendor/composer/version-constraint/normalize-separators',
            'vendor/composer/version-constraint/remove-leading-v',
        ];

        self::assertSame($expected, $names);
    }

    /**
     * @dataProvider provideRuleAndPointerThatIsSkipped
     */
    public function testSkipsReturnsSkipsSatisfiedByPointer(
        string $rule,
        string $pointer
    ): void {
        $skips = self::skipsSatisfiedBy(
            $rule,
            $pointer,
        );

        self::assertNotSame([], $skips);
    }

    /**
     * @return \Generator<string, array{0: string, 1: string}>
     */
    public static function provideRuleAndPointerThatIsSkipped(): iterable
    {
        $pointers = [
            '/config/allow-plugins',
            '/config/preferred-install',
            '/extra/installer-paths',
            '/extra/patches/vendor~1package',
            '/repositories',
            '/scripts/auto-scripts',
        ];

        $rules = [
            'sort/properties-by-schema',
            'sort/properties-by-name',
            'prune/empty-optional-properties',
        ];

        foreach ($rules as $rule) {
            foreach ($pointers as $pointer) {
                $key = \sprintf(
                    '%s at %s',
                    $rule,
                    $pointer,
                );

                yield $key => [
                    $rule,
                    $pointer,
                ];
            }
        }

        $sortedByOtherRules = [
            '/config',
            '/conflict',
            '/provide',
            '/replace',
            '/require',
            '/require-dev',
            '/suggest',
        ];

        $sortRules = [
            'sort/properties-by-schema',
            'sort/properties-by-name',
        ];

        foreach ($sortRules as $rule) {
            foreach ($sortedByOtherRules as $pointer) {
                $key = \sprintf(
                    '%s at %s',
                    $rule,
                    $pointer,
                );

                yield $key => [
                    $rule,
                    $pointer,
                ];
            }
        }
    }

    /**
     * @dataProvider provideRuleAndPointerThatIsNotSkipped
     */
    public function testSkipsReturnsNoSkipSatisfiedByPointer(
        string $rule,
        string $pointer
    ): void {
        $skips = self::skipsSatisfiedBy(
            $rule,
            $pointer,
        );

        self::assertSame([], $skips);
    }

    /**
     * @return \Generator<string, array{0: string, 1: string}>
     */
    public static function provideRuleAndPointerThatIsNotSkipped(): iterable
    {
        $values = [
            'prune at /config' => [
                'prune/empty-optional-properties',
                '/config',
            ],
            'sort by schema at /config/platform' => [
                'sort/properties-by-schema',
                '/config/platform',
            ],
            'sort by name at /extra/patches' => [
                'sort/properties-by-name',
                '/extra/patches',
            ],
            'sort by name at /extra/patches/vendor~1package/0' => [
                'sort/properties-by-name',
                '/extra/patches/vendor~1package/0',
            ],
            'sort by schema at /repositories/0' => [
                'sort/properties-by-schema',
                '/repositories/0',
            ],
            'bin at /repositories' => [
                'vendor/composer/bin/sort-elements',
                '/repositories',
            ],
        ];

        foreach ($values as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * @return list<Skip>
     */
    private static function skipsSatisfiedBy(
        string $rule,
        string $pointer
    ): array {
        $set = Set\Vendor\Composer\ComposerJson::create();

        $jsonPointer = Pointer\JsonPointer::fromJsonString($pointer);

        $skips = [];

        foreach ($set->skips() as $skip) {
            if ($skip->rule()->toString() !== $rule) {
                continue;
            }

            if (!$skip->specification()->isSatisfiedBy($jsonPointer)) {
                continue;
            }

            $skips[] = $skip;
        }

        return $skips;
    }
}
