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

use Localheinz\Json\Normalizer\Exception\InvalidJsonDecodedException;

/**
 * @internal
 */
final class InvalidJsonDecodedExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsInvalidArgumentException(): void
    {
        $this->assertClassExtends(\InvalidArgumentException::class, InvalidJsonDecodedException::class);
    }

    public function testFromDecodedReturnsInvalidJsonDecodedException(): void
    {
        $decoded = $this->faker()->sentence;

        $exception = InvalidJsonDecodedException::fromDecoded($decoded);

        $this->assertInstanceOf(InvalidJsonDecodedException::class, $exception);

        $this->assertSame('The provided data cannot be encoded to JSON.', $exception->getMessage());
        $this->assertSame($decoded, $exception->decoded());
    }
}
