<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit\Exception;

use Localheinz\Json\Normalizer\Exception\InvalidJsonEncodedException;

/**
 * @internal
 *
 * @covers \Localheinz\Json\Normalizer\Exception\InvalidJsonEncodedException
 */
final class InvalidJsonEncodedExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsInvalidArgumentException(): void
    {
        self::assertClassExtends(\InvalidArgumentException::class, InvalidJsonEncodedException::class);
    }

    public function testFromEncodedReturnsInvalidJsonEncodedException(): void
    {
        $encoded = self::faker()->sentence;

        $exception = InvalidJsonEncodedException::fromEncoded($encoded);

        self::assertInstanceOf(InvalidJsonEncodedException::class, $exception);

        $message = \sprintf(
            '"%s" is not valid JSON.',
            $encoded
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($encoded, $exception->encoded());
    }
}
