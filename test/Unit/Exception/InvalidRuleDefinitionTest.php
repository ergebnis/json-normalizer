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
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Exception\InvalidRuleDefinition
 */
final class InvalidRuleDefinitionTest extends Framework\TestCase
{
    public function testBlankDescriptionReturnsInvalidRuleDefinition(): void
    {
        $exception = Exception\InvalidRuleDefinition::blankDescription();

        self::assertSame('Rule description must not be blank.', $exception->getMessage());
    }
}
