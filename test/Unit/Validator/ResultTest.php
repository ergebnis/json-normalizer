<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Validator;

use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Normalizer\Validator;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Validator\Result
 *
 * @internal
 */
final class ResultTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsResultWithoutErrors(): void
    {
        $result = Validator\Result::create();

        self::assertTrue($result->isValid());
        self::assertSame([], $result->errors());
    }

    public function testCreateReturnsResultWithErrors(): void
    {
        $faker = self::faker();

        $errors = [
            $faker->sentence,
            $faker->sentence,
            $faker->sentence,
        ];

        $result = Validator\Result::create(...$errors);

        self::assertFalse($result->isValid());
        self::assertSame($errors, $result->errors());
    }
}
