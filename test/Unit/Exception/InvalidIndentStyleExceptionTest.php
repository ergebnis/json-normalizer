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

use Localheinz\Json\Normalizer\Exception\InvalidIndentStyleException;

/**
 * @internal
 */
final class InvalidIndentStyleExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsInvalidArgumentException(): void
    {
        $this->assertClassExtends(\InvalidArgumentException::class, InvalidIndentStyleException::class);
    }

    public function testFromStyleAndAllowedStylesReturnsInvalidIndentStyleException(): void
    {
        $faker = $this->faker();

        $style = $faker->word;
        $allowedStyles = $faker->words;

        $exception = InvalidIndentStyleException::fromStyleAndAllowedStyles(
            $style,
            ...$allowedStyles
        );

        $this->assertInstanceOf(InvalidIndentStyleException::class, $exception);

        $message = \sprintf(
            'Style needs to be one of "%s", but "%s" is not.',
            \implode('", "', $allowedStyles),
            $style
        );

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($style, $exception->style());
        $this->assertSame($allowedStyles, $exception->allowedStyles());
    }
}
