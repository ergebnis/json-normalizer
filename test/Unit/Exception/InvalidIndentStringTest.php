<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
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

#[Framework\Attributes\CoversClass(Exception\InvalidIndentString::class)]
final class InvalidIndentStringTest extends Framework\TestCase
{
    use Test\Util\Helper;

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
