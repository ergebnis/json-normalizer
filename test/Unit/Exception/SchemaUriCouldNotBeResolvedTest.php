<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
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
 * @covers \Ergebnis\Json\Normalizer\Exception\SchemaUriCouldNotBeResolved
 */
final class SchemaUriCouldNotBeResolvedTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromStringReturnsSchemaUriCouldNotBeResolved(): void
    {
        $value = self::faker()->url();

        $exception = Exception\SchemaUriCouldNotBeResolved::fromString($value);

        $message = \sprintf(
            'Schema URI "%s" could not be resolved.',
            $value,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($value, $exception->value());
    }
}
