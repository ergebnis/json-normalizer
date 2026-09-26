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

use Ergebnis\Json\Normalizer\Change;
use Ergebnis\Json\Normalizer\Result;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Result
 *
 * @uses \Ergebnis\Json\Normalizer\Change
 * @uses \Ergebnis\Json\Normalizer\Rule\Name
 */
final class ResultTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsResult(): void
    {
        $faker = self::faker();

        $input = Parser\Raw::fromString(\sprintf('"%s"', $faker->word()));
        $output = Parser\Raw::fromString(\sprintf('"%s"', $faker->word()));
        $changes = [
            Change::create(
                Rule\Name::fromString('vendor/composer/bin/sort-elements'),
                Parser\Traverser\Path::root(),
            ),
            Change::create(
                Rule\Name::fromString('sort/properties-by-name'),
                Parser\Traverser\Path::root(),
            ),
        ];

        $result = Result::create(
            $input,
            $output,
            ...$changes,
        );

        self::assertSame($input, $result->input());
        self::assertSame($output, $result->output());
        self::assertSame($changes, $result->changes());
    }

    public function testIsChangedReturnsFalseWhenOutputIsSameAsInput(): void
    {
        $value = \sprintf('"%s"', self::faker()->word());

        $input = Parser\Raw::fromString($value);
        $output = Parser\Raw::fromString($value);

        $result = Result::create(
            $input,
            $output,
        );

        self::assertFalse($result->isChanged());
    }

    public function testIsChangedReturnsTrueWhenOutputIsDifferentFromInput(): void
    {
        $value = \sprintf('"%s"', self::faker()->word());

        $input = Parser\Raw::fromString($value);
        $output = Parser\Raw::fromString(\sprintf(
            "%s\n",
            $value,
        ));

        $result = Result::create(
            $input,
            $output,
        );

        self::assertTrue($result->isChanged());
    }
}
