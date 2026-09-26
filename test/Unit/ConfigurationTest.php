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
use Ergebnis\Json\Normalizer\Skip;
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
 * @uses \Ergebnis\Json\Normalizer\Skip
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
                [],
                $one,
                $two,
            ),
            Test\Double\Set\ListedSet::create(
                Set\Name::fromString('@second'),
                [],
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
                [],
                $one,
            ))
            ->withSets(Test\Double\Set\ListedSet::create(
                Set\Name::fromString('@second'),
                [],
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
                [],
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
                [],
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
                [],
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

        $mutated = $configuration->withSets(Test\Double\Set\ListedSet::create(
            Set\Name::fromString('@first'),
            [],
        ));

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

    public function testWithSetsReturnsConfigurationWithSkipsOfSets(): void
    {
        $configuration = Configuration::create()->withSets(
            Test\Double\Set\ListedSet::create(
                Set\Name::fromString('@first'),
                [
                    Skip::create(
                        Rule\Name::fromString('one'),
                        Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/extra/patches')),
                    ),
                ],
            ),
            Test\Double\Set\ListedSet::create(
                Set\Name::fromString('@second'),
                [
                    Skip::create(
                        Rule\Name::fromString('one'),
                        Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/repositories')),
                    ),
                    Skip::create(
                        Rule\Name::fromString('two'),
                        Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
                    ),
                ],
            ),
        );

        $specification = $configuration->skipFor(Rule\Name::fromString('one'));

        self::assertTrue($specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/extra/patches')));
        self::assertTrue($specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/repositories')));
        self::assertFalse($specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/bin')));
    }

    public function testWithSetsAndWithSkipReturnsConfigurationWithSkipsOfBoth(): void
    {
        $configuration = Configuration::create()
            ->withSets(Test\Double\Set\ListedSet::create(
                Set\Name::fromString('@first'),
                [
                    Skip::create(
                        Rule\Name::fromString('one'),
                        Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/extra/patches')),
                    ),
                ],
            ))
            ->withSkip(
                Rule\Name::fromString('one'),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/repositories')),
            );

        $specification = $configuration->skipFor(Rule\Name::fromString('one'));

        self::assertTrue($specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/extra/patches')));
        self::assertTrue($specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/repositories')));
        self::assertFalse($specification->isSatisfiedBy(Pointer\JsonPointer::fromJsonString('/bin')));
    }

    public function testFormatReturnsDetectedFormatWhenNothingIsConfigured(): void
    {
        $detected = self::detectedFormat();

        $configuration = Configuration::create();

        self::assertEquals($detected, $configuration->format($detected));
    }

    public function testFormatReturnsFormatWithConfiguredIndent(): void
    {
        $detected = self::detectedFormat();

        $indent = Parser\Indent::create(
            Parser\IndentSize::fromInt(1),
            Parser\IndentStyle::tab(),
        );

        $configuration = Configuration::create()->withIndent($indent);

        $expected = Parser\Format::create(
            $indent,
            $detected->newLine(),
            $detected->finalNewLine(),
        );

        self::assertEquals($expected, $configuration->format($detected));
    }

    public function testFormatReturnsFormatWithConfiguredNewLine(): void
    {
        $detected = self::detectedFormat();

        $newLine = Parser\NewLine::crLf();

        $configuration = Configuration::create()->withNewLine($newLine);

        $expected = Parser\Format::create(
            $detected->indent(),
            $newLine,
            $detected->finalNewLine(),
        );

        self::assertEquals($expected, $configuration->format($detected));
    }

    public function testFormatReturnsFormatWithConfiguredFinalNewLine(): void
    {
        $detected = self::detectedFormat();

        $finalNewLine = Parser\FinalNewLine::none();

        $configuration = Configuration::create()->withFinalNewLine($finalNewLine);

        $expected = Parser\Format::create(
            $detected->indent(),
            $detected->newLine(),
            $finalNewLine,
        );

        self::assertEquals($expected, $configuration->format($detected));
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

    private static function detectedFormat(): Parser\Format
    {
        return Parser\Format::create(
            Parser\Indent::create(
                Parser\IndentSize::fromInt(4),
                Parser\IndentStyle::space(),
            ),
            Parser\NewLine::lf(),
            Parser\FinalNewLine::present(),
        );
    }
}
