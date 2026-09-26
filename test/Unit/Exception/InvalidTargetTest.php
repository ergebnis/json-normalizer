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

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Exception\InvalidTarget
 */
final class InvalidTargetTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testWithoutNodeClassesReturnsInvalidTarget(): void
    {
        $exception = Exception\InvalidTarget::withoutNodeClasses();

        self::assertSame('Target must name at least one node class.', $exception->getMessage());
        self::assertSame('', $exception->value());
    }

    public function testNotNodeClassReturnsInvalidTarget(): void
    {
        $value = self::faker()->word();

        $exception = Exception\InvalidTarget::notNodeClass($value);

        $message = \sprintf(
            'Target node class "%s" must implement "%s".',
            $value,
            Parser\Node\Node::class,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($value, $exception->value());
    }
}
