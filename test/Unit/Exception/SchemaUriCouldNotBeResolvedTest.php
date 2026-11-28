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

use Ergebnis\Json\Normalizer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Exception\SchemaUriCouldNotBeResolved
 */
final class SchemaUriCouldNotBeResolvedTest extends Framework\TestCase
{
    use Normalizer\Test\Util\Helper;

    public function testDefaults(): void
    {
        $exception = new Normalizer\Exception\SchemaUriCouldNotBeResolved();

        self::assertSame('', $exception->schemaUri());
    }

    public function testFromSchemaUriReturnsSchemaUriCouldNotBeResolvedException(): void
    {
        $schemaUri = self::faker()->url();

        $exception = Normalizer\Exception\SchemaUriCouldNotBeResolved::fromSchemaUri($schemaUri);

        $message = \sprintf(
            'Schema URI "%s" could not be resolved.',
            $schemaUri,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($schemaUri, $exception->schemaUri());
    }
}
