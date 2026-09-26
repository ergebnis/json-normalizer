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
use Ergebnis\Json\Normalizer\RuleVisitor;
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\RuleVisitor
 *
 * @uses \Ergebnis\Json\Normalizer\Configuration
 */
final class RuleVisitorTest extends Framework\TestCase
{
    public function testEnterReturnsKeep(): void
    {
        $node = Parser\Node\ObjectNode::create();
        $path = Parser\Traverser\Path::root();

        $visitor = RuleVisitor::create(Configuration::create());

        $action = $visitor->enter(
            $node,
            $path,
        );

        self::assertEquals(Parser\Traverser\EnterAction::keep(), $action);
    }

    public function testChangesReturnsEmptyListWhenNothingWasVisited(): void
    {
        $visitor = RuleVisitor::create(Configuration::create());

        self::assertSame([], $visitor->changes());
    }
}
