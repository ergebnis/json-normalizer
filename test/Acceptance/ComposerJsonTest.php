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

namespace Ergebnis\Json\Normalizer\Test\Acceptance;

use Ergebnis\Json\Normalizer\Configuration;
use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Runner;
use Ergebnis\Json\Normalizer\Set;
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

/**
 * @coversNothing
 */
final class ComposerJsonTest extends Framework\TestCase
{
    /**
     * @var list<string>
     */
    private const NOT_YET_PORTED = [
        'Json/IsObject',
        'Json/IsObject/HasEntries/Yes/HasProperty/RequireAndRequireDev',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Branch',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Duplicate',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unique',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unsorted',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/Or/ExactVersion',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Duplicate',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unique',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unsorted',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/Or/ExactVersion',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Duplicate',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unique',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unsorted',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Duplicate',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/NotOverlapping',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Overlapping',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unique',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unsorted',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/GreaterThan',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/GreaterThanOrEqual',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Hyphenated',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/LessThan',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/LessThanOrEqual',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Mixed',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/NotEqualTo',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Duplicate',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/NotOverlapping',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Overlapping',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unique',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unsorted',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Wildcard',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/ExactVersion/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/ExactVersion/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Extension',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/InlineAlias',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Invalid',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Caret/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Caret/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/ExactVersion/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/ExactVersion/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThan/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThan/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThanOrEqual/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThanOrEqual/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Hyphenated/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Hyphenated/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThan/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThan/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThanOrEqual/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThanOrEqual/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/NotEqualTo/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/NotEqualTo/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Tilde/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Tilde/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Any',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Caret/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Caret/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThan/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThan/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThanOrEqual/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThanOrEqual/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Hyphenated/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Hyphenated/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThan/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThan/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThanOrEqual/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThanOrEqual/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/NotEqualTo/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/NotEqualTo/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Tilde/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Tilde/Unstable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Any',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Stable',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Branch',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Duplicate',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unique',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unsorted',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/Or/ExactVersion',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Duplicate',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unique',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unsorted',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/Or/ExactVersion',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Duplicate',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unique',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unsorted',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Duplicate',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/NotOverlapping',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Overlapping',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unique',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unsorted',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/GreaterThan',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/GreaterThanOrEqual',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Hyphenated',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/LessThan',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/LessThanOrEqual',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Mixed',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/NotEqualTo',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Duplicate',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/NotOverlapping',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Overlapping',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unique',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unsorted',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Wildcard',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/ExactVersion/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/ExactVersion/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Extension',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/InlineAlias',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Invalid',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Caret/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Caret/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/ExactVersion/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/ExactVersion/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThan/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThan/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThanOrEqual/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThanOrEqual/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Hyphenated/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Hyphenated/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThan/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThan/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThanOrEqual/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThanOrEqual/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/NotEqualTo/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/NotEqualTo/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Tilde/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Tilde/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Any',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Caret/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Caret/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThan/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThan/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThanOrEqual/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThanOrEqual/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Hyphenated/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Hyphenated/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThan/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThan/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThanOrEqual/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThanOrEqual/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/NotEqualTo/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/NotEqualTo/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Tilde/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Tilde/Unstable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Any',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Stable',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Branch',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Duplicate',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unique',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unsorted',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/Or/ExactVersion',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Duplicate',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unique',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unsorted',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/Or/ExactVersion',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Duplicate',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unique',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unsorted',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Duplicate',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/NotOverlapping',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Overlapping',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unique',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unsorted',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/GreaterThan',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/GreaterThanOrEqual',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Hyphenated',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/LessThan',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/LessThanOrEqual',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Mixed',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/NotEqualTo',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Duplicate',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/NotOverlapping',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Overlapping',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unique',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unsorted',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Wildcard',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/ExactVersion/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/ExactVersion/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Extension',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/InlineAlias',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Invalid',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Caret/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Caret/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/ExactVersion/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/ExactVersion/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThan/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThan/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThanOrEqual/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThanOrEqual/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Hyphenated/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Hyphenated/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThan/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThan/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThanOrEqual/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThanOrEqual/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/NotEqualTo/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/NotEqualTo/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Tilde/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Tilde/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Any',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Caret/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Caret/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThan/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThan/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThanOrEqual/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThanOrEqual/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Hyphenated/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Hyphenated/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThan/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThan/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThanOrEqual/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThanOrEqual/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/NotEqualTo/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/NotEqualTo/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Tilde/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Tilde/Unstable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Any',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Stable',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Branch',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Duplicate',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unique',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unsorted',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/Or/ExactVersion',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Duplicate',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unique',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unsorted',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/Or/ExactVersion',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Duplicate',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unique',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unsorted',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Duplicate',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/NotOverlapping',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Overlapping',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unique',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unsorted',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/GreaterThan',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/GreaterThanOrEqual',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Hyphenated',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/LessThan',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/LessThanOrEqual',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Mixed',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/NotEqualTo',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Duplicate',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/NotOverlapping',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Overlapping',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unique',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unsorted',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Wildcard',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/ExactVersion/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/ExactVersion/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Extension',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/InlineAlias',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Invalid',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Caret/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Caret/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/ExactVersion/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/ExactVersion/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThan/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThan/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThanOrEqual/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThanOrEqual/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Hyphenated/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Hyphenated/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThan/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThan/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThanOrEqual/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThanOrEqual/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/NotEqualTo/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/NotEqualTo/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Tilde/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Tilde/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Any',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Caret/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Caret/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThan/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThan/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThanOrEqual/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThanOrEqual/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Hyphenated/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Hyphenated/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThan/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThan/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThanOrEqual/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThanOrEqual/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/NotEqualTo/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/NotEqualTo/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Tilde/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Tilde/Unstable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Any',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Stable',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Branch',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Duplicate',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unique',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unsorted',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/Or/ExactVersion',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Duplicate',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unique',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unsorted',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/Or/ExactVersion',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Duplicate',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unique',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unsorted',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Duplicate',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/NotOverlapping',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Overlapping',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unique',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unsorted',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/GreaterThan',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/GreaterThanOrEqual',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Hyphenated',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/LessThan',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/LessThanOrEqual',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Mixed',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/NotEqualTo',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Duplicate',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/NotOverlapping',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Overlapping',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unique',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unsorted',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Wildcard',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/ExactVersion/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/ExactVersion/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Extension',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/InlineAlias',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Invalid',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Caret/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Caret/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/ExactVersion/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/ExactVersion/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThan/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThan/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThanOrEqual/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/GreaterThanOrEqual/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Hyphenated/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Hyphenated/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThan/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThan/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThanOrEqual/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/LessThanOrEqual/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/NotEqualTo/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/NotEqualTo/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Tilde/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Tilde/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Any',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/LeadingV/Wildcard/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Caret/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Caret/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThan/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThan/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThanOrEqual/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/GreaterThanOrEqual/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Hyphenated/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Hyphenated/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThan/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThan/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThanOrEqual/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/LessThanOrEqual/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/NotEqualTo/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/NotEqualTo/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Tilde/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Tilde/Unstable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Any',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Stable',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Unstable',
    ];

    /**
     * @var array<string, string>
     */
    private const SECTIONS = [
        'conflict' => 'Conflict',
        'provide' => 'Provide',
        'replace' => 'Replace',
        'require' => 'Require',
        'require-dev' => 'RequireDev',
    ];

    /**
     * @dataProvider provideCase
     */
    public function testNormalizeNormalizesInput(
        string $key,
        string $input,
        string $output
    ): void {
        $raw = Parser\Raw::fromString($input);

        $runner = Runner::create(self::configuration());

        if (\in_array($key, self::NOT_YET_PORTED, true)) {
            try {
                $result = $runner->normalize($raw);
            } catch (\Exception $exception) {
                $this->addToAssertionCount(1);

                return;
            }

            self::assertNotSame($output, $result->output()->toString(), \sprintf(
                'Case "%s" passes; remove it from NOT_YET_PORTED.',
                $key,
            ));

            return;
        }

        $result = $runner->normalize($raw);

        self::assertSame($output, $result->output()->toString());
    }

    /**
     * @return \Generator<string, array{0: string, 1: string, 2: string}>
     */
    public static function provideCase(): iterable
    {
        $cases = [];

        foreach (self::casesIn(self::directory()) as $key => $case) {
            if (0 === \strpos($key, 'Rejects/')) {
                continue;
            }

            if (0 === \strpos($key, 'Template/')) {
                continue;
            }

            $cases[$key] = $case;
        }

        $templates = self::casesIn(\sprintf(
            '%s/Template',
            self::directory(),
        ));

        foreach (self::SECTIONS as $section => $directory) {
            foreach ($templates as $key => $template) {
                $cases[\sprintf(
                    'Template/%s/%s',
                    $directory,
                    $key,
                )] = \array_map(static function (string $contents) use ($section): string {
                    return \str_replace(
                        '"value-contains-packages-and-version-constraints"',
                        \sprintf(
                            '"%s"',
                            $section,
                        ),
                        $contents,
                    );
                }, $template);
            }
        }

        \ksort($cases);

        foreach ($cases as $key => $case) {
            yield $key => [
                $key,
                $case['input'],
                $case['output'],
            ];
        }
    }

    /**
     * @dataProvider provideRejectedCase
     */
    public function testNormalizeThrowsInputInvalidAccordingToSchemaWhenInputIsInvalid(string $input): void
    {
        $raw = Parser\Raw::fromString($input);

        $runner = Runner::create(self::configuration());

        $this->expectException(Exception\InputInvalidAccordingToSchema::class);

        $runner->normalize($raw);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideRejectedCase(): iterable
    {
        $cases = self::casesIn(\sprintf(
            '%s/Rejects',
            self::directory(),
        ));

        \ksort($cases);

        foreach ($cases as $key => $case) {
            yield $key => [
                $case['input'],
            ];
        }
    }

    private static function configuration(): Configuration
    {
        return Configuration::create()
            ->withSchema(self::schemaUri())
            ->withSets(Set\Vendor\Composer\ComposerJson::create());
    }

    private static function schemaUri(): string
    {
        return \sprintf(
            'file://%s',
            \realpath(\sprintf(
                '%s/../Fixture/Vendor/Composer/schema.json',
                __DIR__,
            )),
        );
    }

    private static function directory(): string
    {
        return \sprintf(
            '%s/../Fixture/Acceptance/ComposerJson',
            __DIR__,
        );
    }

    /**
     * @return array<string, array{input: string, output: string}>
     */
    private static function casesIn(string $directory): array
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(
            $directory,
            \FilesystemIterator::SKIP_DOTS,
        ));

        $cases = [];

        foreach ($iterator as $fileInfo) {
            /** @var \SplFileInfo $fileInfo */
            if ('input.json' !== $fileInfo->getBasename()) {
                continue;
            }

            $key = \substr(
                $fileInfo->getPath(),
                \strlen($directory) + 1,
            );

            $input = (string) \file_get_contents($fileInfo->getPathname());
            $output = $input;

            $outputFile = \sprintf(
                '%s/output.json',
                $fileInfo->getPath(),
            );

            if (\is_file($outputFile)) {
                $output = (string) \file_get_contents($outputFile);
            }

            $cases[$key] = [
                'input' => $input,
                'output' => $output,
            ];
        }

        return $cases;
    }
}
