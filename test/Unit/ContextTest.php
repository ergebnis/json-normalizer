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

use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Context
 */
final class ContextTest extends Framework\TestCase
{
    public function testCreateReturnsContext(): void
    {
        $path = Parser\Traverser\Path::root();

        $context = Context::create($path);

        self::assertSame($path, $context->path());
    }
}
