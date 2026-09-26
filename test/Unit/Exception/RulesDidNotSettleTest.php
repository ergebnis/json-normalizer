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

namespace Ergebnis\Json\Normalizer\Test\Unit\Exception;

use Ergebnis\Json\Normalizer\Change;
use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Exception\RulesDidNotSettle
 *
 * @uses \Ergebnis\Json\Normalizer\Change
 * @uses \Ergebnis\Json\Normalizer\Rule\Name
 */
final class RulesDidNotSettleTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testAfterReturnsRulesDidNotSettle(): void
    {
        $passes = self::faker()->numberBetween(2, 20);
        $changes = [
            Change::create(
                Rule\Name::fromString('vendor/composer/bin/sort-elements'),
                Parser\Traverser\Path::root()->property(
                    Parser\Index::fromInt(0),
                    Parser\Node\StringNode::fromString('bin'),
                ),
            ),
            Change::create(
                Rule\Name::fromString('sort/properties-by-name'),
                Parser\Traverser\Path::root(),
            ),
        ];

        $exception = Exception\RulesDidNotSettle::after(
            $passes,
            ...$changes,
        );

        $message = \sprintf(
            'Rules did not settle after %d passes; the last pass changed "%s" at "%s" and "%s" at "%s".',
            $passes,
            'vendor/composer/bin/sort-elements',
            '/bin',
            'sort/properties-by-name',
            '',
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($passes, $exception->passes());
        self::assertSame($changes, $exception->changes());
    }
}
