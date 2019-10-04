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

use Localheinz\Json\Normalizer\Exception\InvalidIndentStyleException;

/**
 * @internal
 * @coversNothing
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

        /** @var string[] $allowedStyles */
        $allowedStyles = $faker->words;

        $exception = InvalidIndentStyleException::fromStyleAndAllowedStyles(
            $style,
            ...$allowedStyles
        );

        self::assertInstanceOf(InvalidIndentStyleException::class, $exception);

        $message = \sprintf(
            'Style needs to be one of "%s", but "%s" is not.',
            \implode('", "', $allowedStyles),
            $style
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($style, $exception->style());
        self::assertSame($allowedStyles, $exception->allowedStyles());
    }
}
