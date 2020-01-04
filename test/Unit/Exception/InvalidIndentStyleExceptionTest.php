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

use Ergebnis\Json\Normalizer\Exception\InvalidIndentStyleException;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\InvalidIndentStyleException
 */
final class InvalidIndentStyleExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsInvalidArgumentException(): void
    {
        self::assertClassExtends(\InvalidArgumentException::class, InvalidIndentStyleException::class);
    }

    public function testFromStyleAndAllowedStylesReturnsInvalidIndentStyleException(): void
    {
        $faker = self::faker();

        $style = $faker->word;

        /** @var string[] $allowedStyles */
        $allowedStyles = $faker->words;

        $exception = InvalidIndentStyleException::fromStyleAndAllowedStyles(
            $style,
            ...$allowedStyles
        );

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
