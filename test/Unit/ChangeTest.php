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
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Change
 *
 * @uses \Ergebnis\Json\Normalizer\Rule\Name
 */
final class ChangeTest extends Framework\TestCase
{
    public function testCreateReturnsChange(): void
    {
        $rule = Rule\Name::fromString('vendor/composer/bin/sort-elements');
        $path = Parser\Traverser\Path::root();

        $change = Change::create(
            $rule,
            $path,
        );

        self::assertSame($rule, $change->rule());
        self::assertSame($path, $change->path());
    }
}
