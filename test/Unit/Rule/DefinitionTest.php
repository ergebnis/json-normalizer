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
 * @covers \Ergebnis\Json\Normalizer\Rule\Definition
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidRuleDefinition
 * @uses \Ergebnis\Json\Normalizer\Rule\Example
 */
final class DefinitionTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider provideBlankDescription
     */
    public function testCreateThrowsInvalidRuleDefinitionWhenDescriptionIsBlank(string $description): void
    {
        $faker = self::faker();

        $example = Rule\Example::create(
            $faker->sentence(),
            $faker->sentence(),
        );

        $this->expectException(Exception\InvalidRuleDefinition::class);

        Rule\Definition::create(
            $description,
            $example,
        );
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideBlankDescription(): iterable
    {
        $values = [
            'empty' => '',
            'space' => ' ',
            'new-line-and-tab' => "\n\t",
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    public function testCreateReturnsDefinitionWhenDescriptionIsNotBlank(): void
    {
        $faker = self::faker();

        $description = $faker->sentence();

        $example = Rule\Example::create(
            $faker->sentence(),
            $faker->sentence(),
        );

        $definition = Rule\Definition::create(
            $description,
            $example,
        );

        self::assertSame($description, $definition->description());
        self::assertSame([$example], $definition->examples());
    }

    public function testCreateReturnsDefinitionWithExamplesInOrder(): void
    {
        $faker = self::faker();

        $one = Rule\Example::create(
            $faker->sentence(),
            $faker->sentence(),
        );

        $two = Rule\Example::create(
            $faker->sentence(),
            $faker->sentence(),
        );

        $three = Rule\Example::create(
            $faker->sentence(),
            $faker->sentence(),
        );

        $definition = Rule\Definition::create(
            $faker->sentence(),
            $one,
            $two,
            $three,
        );

        $expected = [
            $one,
            $two,
            $three,
        ];

        self::assertSame($expected, $definition->examples());
    }
}
