<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit\Exception;

use Localheinz\Json\Normalizer\Exception\InvalidJsonException;

/**
 * @internal
 */
final class InvalidJsonExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsInvalidArgumentException(): void
    {
        $this->assertClassExtends(\InvalidArgumentException::class, InvalidJsonException::class);
    }

    public function testFromEncodedReturnsInvalidJsonException(): void
    {
        $encoded = $this->faker()->sentence;

        $exception = InvalidJsonException::fromEncoded($encoded);

        $this->assertInstanceOf(InvalidJsonException::class, $exception);

        $message = \sprintf(
            '"%s" is not valid JSON.',
            $encoded
        );

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($encoded, $exception->encoded());
    }
}
