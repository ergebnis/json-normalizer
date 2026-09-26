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

namespace Ergebnis\Json\Normalizer\Test\Unit\Rule;

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Rule\Name
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidRuleName
 */
final class NameTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider provideValueThatIsNotKebabCaseSegments
     */
    public function testFromStringThrowsInvalidRuleNameWhenValueIsNotKebabCaseSegments(string $value): void
    {
        $this->expectException(Exception\InvalidRuleName::class);

        Rule\Name::fromString($value);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideValueThatIsNotKebabCaseSegments(): iterable
    {
        $values = [
            'empty' => '',
            'leading-slash' => '/sort/properties-by-name',
            'trailing-slash' => 'sort/properties-by-name/',
            'double-slash' => 'sort//properties-by-name',
            'upper-case' => 'Sort/properties-by-name',
            'snake-case' => 'sort/properties_by_name',
            'leading-hyphen' => 'sort/-properties',
            'trailing-hyphen' => 'sort/properties-',
            'double-hyphen' => 'sort/properties--by-name',
            'leading-digit' => '1sort/properties',
            'whitespace' => 'sort/properties by name',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @dataProvider provideValueThatIsKebabCaseSegments
     */
    public function testFromStringReturnsNameWhenValueIsKebabCaseSegments(string $value): void
    {
        $name = Rule\Name::fromString($value);

        self::assertSame($value, $name->toString());
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideValueThatIsKebabCaseSegments(): iterable
    {
        $values = [
            'one-segment' => 'sort',
            'two-segments' => 'sort/properties-by-name',
            'four-segments' => 'vendor/composer/bin/sort-elements',
            'digits' => 'vendor/composer/psr4/sort',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $one = Rule\Name::fromString('sort/properties-by-name');
        $two = Rule\Name::fromString('vendor/composer/bin/sort-elements');

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $one = Rule\Name::fromString('vendor/composer/bin/sort-elements');
        $two = Rule\Name::fromString('vendor/composer/bin/sort-elements');

        self::assertTrue($one->equals($two));
    }
}
