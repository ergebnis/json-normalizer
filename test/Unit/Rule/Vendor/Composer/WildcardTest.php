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

namespace Ergebnis\Json\Normalizer\Test\Unit\Rule\Vendor\Composer;

use Ergebnis\Json\Normalizer\Rule;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Wildcard
 */
final class WildcardTest extends Framework\TestCase
{
    /**
     * @dataProvider provideValueWithoutWildcardNotAtEnd
     */
    public function testContainsWildcardNotAtEndReturnsFalseWhenValueDoesNotContainWildcardNotAtEnd(string $value): void
    {
        self::assertFalse(Rule\Vendor\Composer\Wildcard::containsWildcardNotAtEnd($value));
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideValueWithoutWildcardNotAtEnd(): iterable
    {
        $values = [
            'empty' => '',
            'no-wildcard' => 'foo/bar',
            'wildcard-only' => '*',
            'wildcard-at-end' => 'foo/*',
            'wildcards-at-end' => 'foo/**',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @dataProvider provideValueWithWildcardNotAtEnd
     */
    public function testContainsWildcardNotAtEndReturnsTrueWhenValueContainsWildcardNotAtEnd(string $value): void
    {
        self::assertTrue(Rule\Vendor\Composer\Wildcard::containsWildcardNotAtEnd($value));
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideValueWithWildcardNotAtEnd(): iterable
    {
        $values = [
            'wildcard-at-start' => '*/bar',
            'wildcard-in-middle' => 'foo/*-plugin',
            'wildcards-in-middle-and-at-end' => 'foo/*-*',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @dataProvider provideOneTwoAndExpected
     */
    public function testCompareReturnsComparisonWithWildcardAfterEveryOtherCharacter(
        string $one,
        string $two,
        int $expected
    ): void {
        $comparison = Rule\Vendor\Composer\Wildcard::compare(
            $one,
            $two,
        );

        self::assertSame($expected, $comparison <=> 0);
    }

    /**
     * @return \Generator<string, array{0: string, 1: string, 2: int}>
     */
    public static function provideOneTwoAndExpected(): iterable
    {
        $values = [
            'equal' => [
                'foo/bar',
                'foo/bar',
                0,
            ],
            'less' => [
                'bar/baz',
                'foo/bar',
                -1,
            ],
            'greater' => [
                'foo/bar',
                'bar/baz',
                1,
            ],
            'wildcard-after-name-in-group' => [
                'foo/*',
                'foo/zzz',
                1,
            ],
            'name-before-wildcard-in-group' => [
                'foo/zzz',
                'foo/*',
                -1,
            ],
            'wildcard-only-after-everything' => [
                '*',
                'zzz/zzz',
                1,
            ],
        ];

        foreach ($values as $key => $value) {
            yield $key => $value;
        }
    }
}
