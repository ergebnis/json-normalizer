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

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\InvalidIndentSize
 */
final class InvalidIndentSizeTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testDefaults(): void
    {
        $exception = new Exception\InvalidIndentSize();

        self::assertSame(0, $exception->minimumSize());
        self::assertSame(0, $exception->size());
    }

    public function testFromSizeAndMinimumSizeReturnsInvalidIndentSizeException(): void
    {
        $faker = self::faker();

        $size = $faker->numberBetween(1);
        $minimumSize = $faker->numberBetween(1);

        $exception = Exception\InvalidIndentSize::fromSizeAndMinimumSize(
            $size,
            $minimumSize,
        );

        $message = \sprintf(
            'Size needs to be greater than %d, but %d is not.',
            $minimumSize - 1,
            $size,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($size, $exception->size());
        self::assertSame($minimumSize, $exception->minimumSize());
    }
}
