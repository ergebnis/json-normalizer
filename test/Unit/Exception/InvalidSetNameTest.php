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
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Exception\InvalidSetName
 */
final class InvalidSetNameTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testNotAtSignFollowedByKebabCaseSegmentsReturnsInvalidSetName(): void
    {
        $value = self::faker()->sentence();

        $exception = Exception\InvalidSetName::notAtSignFollowedByKebabCaseSegments($value);

        $message = \sprintf(
            'Set name "%s" must consist of "@" followed by kebab-case segments separated by slashes.',
            $value,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($value, $exception->value());
    }
}
