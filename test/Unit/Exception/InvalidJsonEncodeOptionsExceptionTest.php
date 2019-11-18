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

use Localheinz\Json\Normalizer\Exception\InvalidJsonEncodeOptionsException;

/**
 * @internal
 *
 * @covers \Localheinz\Json\Normalizer\Exception\InvalidJsonEncodeOptionsException
 */
final class InvalidJsonEncodeOptionsExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsInvalidArgumentException(): void
    {
        self::assertClassExtends(\InvalidArgumentException::class, InvalidJsonEncodeOptionsException::class);
    }

    public function testFromJsonEncodeOptionsReturnsInvalidJsonEncodeOptionsException(): void
    {
        $jsonEncodeOptions = self::faker()->randomNumber();

        $exception = InvalidJsonEncodeOptionsException::fromJsonEncodeOptions($jsonEncodeOptions);

        self::assertInstanceOf(InvalidJsonEncodeOptionsException::class, $exception);

        $message = \sprintf(
            '"%s" is not valid options for json_encode().',
            $jsonEncodeOptions
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($jsonEncodeOptions, $exception->jsonEncodeOptions());
    }
}
