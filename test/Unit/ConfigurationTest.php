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

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Normalizer\Configuration;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Set;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Configuration
 *
 * @uses \Ergebnis\Json\Normalizer\Rule\Name
 * @uses \Ergebnis\Json\Normalizer\Rule\Target
 * @uses \Ergebnis\Json\Normalizer\Set\Name
 */
final class ConfigurationTest extends Framework\TestCase
{
    public function testCreateReturnsConfigurationWithoutRules(): void
    {
        $configuration = Configuration::create();

        self::assertSame([], $configuration->rules());
    }

    public function testWithSetsReturnsConfigurationWithRulesOfSetsInOrder(): void
    {
        $one = self::rule('one');
        $two = self::rule('two');
        $three = self::rule('three');

        $configuration = Configuration::create()->withSets(
            Test\Double\Set\ListedSet::create(
                Set\Name::fromString('@first'),
                $one,
                $two,
            ),
            Test\Double\Set\ListedSet::create(
                Set\Name::fromString('@second'),
                $three,
            ),
        );

        $expected = [
            $one,
            $two,
            $three,
        ];

        self::assertSame($expected, $configuration->rules());
    }

    public function testWithSetsReturnsConfigurationWithRulesOfSetsOfEveryInvocation(): void
    {
        $one = self::rule('one');
        $two = self::rule('two');

        $configuration = Configuration::create()
            ->withSets(Test\Double\Set\ListedSet::create(
                Set\Name::fromString('@first'),
                $one,
            ))
            ->withSets(Test\Double\Set\ListedSet::create(
                Set\Name::fromString('@second'),
                $two,
            ));

        $expected = [
            $one,
            $two,
        ];

        self::assertSame($expected, $configuration->rules());
    }

    public function testWithRulesReturnsConfigurationWithListedRulesAfterRulesOfSets(): void
    {
        $one = self::rule('one');
        $two = self::rule('two');

        $configuration = Configuration::create()
            ->withRules($two)
            ->withSets(Test\Double\Set\ListedSet::create(
                Set\Name::fromString('@first'),
                $one,
            ));

        $expected = [
            $one,
            $two,
        ];

        self::assertSame($expected, $configuration->rules());
    }

    public function testWithRulesReturnsConfigurationWhereListedRuleReplacesRuleOfSetWithSameNameAtItsPosition(): void
    {
        $one = self::rule('one');
        $two = self::rule('two');
        $replacement = self::rule('one');

        $configuration = Configuration::create()
            ->withRules($replacement)
            ->withSets(Test\Double\Set\ListedSet::create(
                Set\Name::fromString('@first'),
                $one,
                $two,
            ));

        $expected = [
            $replacement,
            $two,
        ];

        self::assertSame($expected, $configuration->rules());
    }

    public function testWithRulesReturnsConfigurationWithRulesOfEveryInvocation(): void
    {
        $one = self::rule('one');
        $two = self::rule('two');

        $configuration = Configuration::create()
            ->withRules($one)
            ->withRules($two);

        $expected = [
            $one,
            $two,
        ];

        self::assertSame($expected, $configuration->rules());
    }

    public function testWithoutRulesReturnsConfigurationWithoutRulesWithNames(): void
    {
        $one = self::rule('one');
        $two = self::rule('two');

        $configuration = Configuration::create()
            ->withoutRules(Rule\Name::fromString('one'))
            ->withSets(Test\Double\Set\ListedSet::create(
                Set\Name::fromString('@first'),
                $one,
                $two,
            ));

        $expected = [
            $two,
        ];

        self::assertSame($expected, $configuration->rules());
    }

    public function testWithoutRulesReturnsConfigurationWithoutRulesWithNamesOfEveryInvocation(): void
    {
        $one = self::rule('one');
        $two = self::rule('two');
        $three = self::rule('three');

        $configuration = Configuration::create()
            ->withoutRules(Rule\Name::fromString('one'))
            ->withoutRules(Rule\Name::fromString('two'))
            ->withRules(
                $one,
                $two,
                $three,
            );

        $expected = [
            $three,
        ];

        self::assertSame($expected, $configuration->rules());
    }

    public function testWithSetsReturnsNewConfiguration(): void
    {
        $configuration = Configuration::create();

        $mutated = $configuration->withSets(Test\Double\Set\ListedSet::create(Set\Name::fromString('@first')));

        self::assertNotSame($configuration, $mutated);
    }

    public function testSkipForReturnsSpecificationThatIsNeverSatisfiedWhenRuleHasNoSkips(): void
    {
        $configuration = Configuration::create();

        $specification = $configuration->skipFor(Rule\Name::fromString('one'));

        self::assertFalse($specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/bin')));
    }

    public function testSkipForReturnsSpecificationSatisfiedByAnySkipOfRule(): void
    {
        $configuration = Configuration::create()
            ->withSkip(
                Rule\Name::fromString('one'),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/extra/patches')),
            )
            ->withSkip(
                Rule\Name::fromString('one'),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/repositories')),
            )
            ->withSkip(
                Rule\Name::fromString('two'),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
            );

        $specification = $configuration->skipFor(Rule\Name::fromString('one'));

        self::assertTrue($specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/extra/patches')));
        self::assertTrue($specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/repositories')));
        self::assertFalse($specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/bin')));
    }

    private static function rule(string $name): Rule
    {
        return Test\Double\Rule\KeepingRule::create(
            Rule\Name::fromString($name),
            Rule\Target::create(
                Pointer\Specification::always(),
                Parser\Node\ArrayNode::class,
            ),
        );
    }
}
