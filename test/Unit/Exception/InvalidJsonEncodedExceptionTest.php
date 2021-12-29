<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Exception;

use Ergebnis\Json\Normalizer\Exception;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodedException
 */
final class InvalidJsonEncodedExceptionTest extends AbstractExceptionTestCase
{
    public function testDefaults(): void
    {
        $exception = new Exception\InvalidJsonEncodedException();

        self::assertSame('', $exception->encoded());
    }

    public function testFromEncodedReturnsInvalidJsonEncodedException(): void
    {
        $encoded = self::faker()->sentence();

        $exception = Exception\InvalidJsonEncodedException::fromEncoded($encoded);

        $message = \sprintf(
            '"%s" is not valid JSON.',
            $encoded,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($encoded, $exception->encoded());
    }
}
