<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Exception;

use Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodedException;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodedException
 */
final class InvalidJsonEncodedExceptionTest extends AbstractExceptionTestCase
{
    public function testFromEncodedReturnsInvalidJsonEncodedException(): void
    {
        $encoded = self::faker()->sentence;

        $exception = InvalidJsonEncodedException::fromEncoded($encoded);

        $message = \sprintf(
            '"%s" is not valid JSON.',
            $encoded
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($encoded, $exception->encoded());
    }
}
