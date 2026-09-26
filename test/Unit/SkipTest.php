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

use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Skip;
use Ergebnis\Json\Pointer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Skip
 *
 * @uses \Ergebnis\Json\Normalizer\Rule\Name
 */
final class SkipTest extends Framework\TestCase
{
    public function testCreateReturnsSkip(): void
    {
        $rule = Rule\Name::fromString('vendor/composer/bin/sort-elements');
        $specification = Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin'));

        $skip = Skip::create(
            $rule,
            $specification,
        );

        self::assertSame($rule, $skip->rule());
        self::assertSame($specification, $skip->specification());
    }
}
