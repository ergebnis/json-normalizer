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

use Localheinz\Json\Normalizer\Exception\InvalidIndentSizeException;

/**
 * @internal
 */
final class InvalidIndentSizeExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsInvalidArgumentException(): void
    {
        $this->assertClassExtends(\InvalidArgumentException::class, InvalidIndentSizeException::class);
    }

    public function testFromSizeAndMinimumSizeReturnsInvalidIndentSizeException(): void
    {
        $faker = $this->faker();

        $size = $faker->numberBetween(1);
        $minimumSize = $faker->numberBetween(1);

        $exception = InvalidIndentSizeException::fromSizeAndMinimumSize(
            $size,
            $minimumSize
        );

        $this->assertInstanceOf(InvalidIndentSizeException::class, $exception);

        $message = \sprintf(
            'Size needs to be greater than %d, but %d is not.',
            $minimumSize - 1,
            $size
        );

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($size, $exception->size());
        $this->assertSame($minimumSize, $exception->minimumSize());
    }
}
