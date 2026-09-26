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

namespace Ergebnis\Json\Normalizer\Test\Unit\Rule\Vendor\Composer\Config;

use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Test;

/**
 * @covers \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Config\SortProperties
 *
 * @uses \Ergebnis\Json\Normalizer\Change
 * @uses \Ergebnis\Json\Normalizer\Configuration
 * @uses \Ergebnis\Json\Normalizer\Context
 * @uses \Ergebnis\Json\Normalizer\Result
 * @uses \Ergebnis\Json\Normalizer\Rule\Action
 * @uses \Ergebnis\Json\Normalizer\Rule\Definition
 * @uses \Ergebnis\Json\Normalizer\Rule\Example
 * @uses \Ergebnis\Json\Normalizer\Rule\Name
 * @uses \Ergebnis\Json\Normalizer\Rule\Target
 * @uses \Ergebnis\Json\Normalizer\RuleVisitor
 * @uses \Ergebnis\Json\Normalizer\Runner
 * @uses \Ergebnis\Json\Normalizer\Schema
 * @uses \Ergebnis\Json\Normalizer\SchemaLoader
 * @uses \Ergebnis\Json\Normalizer\SchemaResolver
 */
final class SortPropertiesTest extends Test\Util\AbstractRuleTestCase
{
    protected static function rule(): Rule
    {
        return Rule\Vendor\Composer\Config\SortProperties::create();
    }
}
