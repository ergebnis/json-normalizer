<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2022 Andreas Möller
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
 * @covers \Ergebnis\Json\Normalizer\Exception\InvalidIndentString
 */
final class InvalidIndentStringTest extends AbstractExceptionTestCase
{
    public function testDefaults(): void
    {
        $exception = new Exception\InvalidIndentString();

        self::assertSame('', $exception->string());
    }

    public function testFromSizeAndMinimumSizeReturnsInvalidIndentStringException(): void
    {
        $string = self::faker()->word();

        $exception = Exception\InvalidIndentString::fromString($string);

        $message = \sprintf(
            '"%s" is not a valid indent string',
            $string,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($string, $exception->string());
    }
}
