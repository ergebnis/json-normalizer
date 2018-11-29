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

use Localheinz\Json\Normalizer\Exception\InvalidNewLineStringException;

/**
 * @internal
 */
final class InvalidNewLineStringExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsInvalidArgumentException(): void
    {
        $this->assertClassExtends(\InvalidArgumentException::class, InvalidNewLineStringException::class);
    }

    public function testFromSizeAndMinimumSizeReturnsInvalidIndentStringException(): void
    {
        $string = $this->faker()->word;

        $exception = InvalidNewLineStringException::fromString($string);

        self::assertInstanceOf(InvalidNewLineStringException::class, $exception);

        $message = \sprintf(
            '"%s" is not a valid new-line character sequence.',
            $string
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($string, $exception->string());
    }
}
