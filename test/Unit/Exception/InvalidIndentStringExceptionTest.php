<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Exception;

use Ergebnis\Json\Normalizer\Exception\InvalidIndentStringException;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\InvalidIndentStringException
 */
final class InvalidIndentStringExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsInvalidArgumentException(): void
    {
        self::assertClassExtends(\InvalidArgumentException::class, InvalidIndentStringException::class);
    }

    public function testFromSizeAndMinimumSizeReturnsInvalidIndentStringException(): void
    {
        $string = self::faker()->word;

        $exception = InvalidIndentStringException::fromString($string);

        self::assertInstanceOf(InvalidIndentStringException::class, $exception);

        $message = \sprintf(
            '"%s" is not a valid indent string',
            $string
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($string, $exception->string());
    }
}
