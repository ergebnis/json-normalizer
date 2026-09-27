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

use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Rule\Example
 */
final class ExampleTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsExample(): void
    {
        $faker = self::faker();

        $input = $faker->sentence();
        $output = $faker->sentence();

        $example = Rule\Example::create(
            $input,
            $output,
        );

        self::assertSame($input, $example->input());
        self::assertSame($output, $example->output());
        self::assertNull($example->schema());
    }

    public function testWithSchemaReturnsExampleWithSchema(): void
    {
        $faker = self::faker();

        $input = $faker->sentence();
        $output = $faker->sentence();
        $schema = $faker->sentence();

        $example = Rule\Example::create(
            $input,
            $output,
        );

        $mutated = $example->withSchema($schema);

        self::assertNotSame($example, $mutated);
        self::assertSame($input, $mutated->input());
        self::assertSame($output, $mutated->output());
        self::assertSame($schema, $mutated->schema());
    }
}
