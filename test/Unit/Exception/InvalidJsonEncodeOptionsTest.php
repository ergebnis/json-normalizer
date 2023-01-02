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

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodeOptions
 */
final class InvalidJsonEncodeOptionsTest extends AbstractExceptionTestCase
{
    public function testDefaults(): void
    {
        $exception = new Exception\InvalidJsonEncodeOptions();

        self::assertSame(0, $exception->jsonEncodeOptions());
    }

    public function testFromJsonEncodeOptionsReturnsInvalidJsonEncodeOptionsException(): void
    {
        $jsonEncodeOptions = self::faker()->randomNumber();

        $exception = Exception\InvalidJsonEncodeOptions::fromJsonEncodeOptions($jsonEncodeOptions);

        $message = \sprintf(
            '"%s" is not valid options for json_encode().',
            $jsonEncodeOptions,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($jsonEncodeOptions, $exception->jsonEncodeOptions());
    }
}
