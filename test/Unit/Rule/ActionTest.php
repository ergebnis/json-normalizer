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
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Rule\Action
 */
final class ActionTest extends Framework\TestCase
{
    public function testKeepReturnsAction(): void
    {
        $action = Rule\Action::keep();

        self::assertTrue($action->isKeep());
        self::assertFalse($action->isRemove());
        self::assertNull($action->replacement());
    }

    public function testReplaceReturnsAction(): void
    {
        $node = Parser\Node\ArrayNode::create();

        $action = Rule\Action::replace($node);

        self::assertFalse($action->isKeep());
        self::assertFalse($action->isRemove());
        self::assertSame($node, $action->replacement());
    }

    public function testRemoveReturnsAction(): void
    {
        $action = Rule\Action::remove();

        self::assertFalse($action->isKeep());
        self::assertTrue($action->isRemove());
        self::assertNull($action->replacement());
    }
}
