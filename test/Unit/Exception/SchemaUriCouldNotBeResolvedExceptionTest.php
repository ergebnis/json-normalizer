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

use Ergebnis\Json\Normalizer\Exception\SchemaUriCouldNotBeResolvedException;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\SchemaUriCouldNotBeResolvedException
 */
final class SchemaUriCouldNotBeResolvedExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsRuntimeException(): void
    {
        self::assertClassExtends(\RuntimeException::class, SchemaUriCouldNotBeResolvedException::class);
    }

    public function testFromSchemaUriReturnsSchemaUriCouldNotBeResolvedException(): void
    {
        $schemaUri = self::faker()->url;

        $exception = SchemaUriCouldNotBeResolvedException::fromSchemaUri($schemaUri);

        self::assertInstanceOf(SchemaUriCouldNotBeResolvedException::class, $exception);

        $message = \sprintf(
            'Schema URI "%s" could not be resolved.',
            $schemaUri
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($schemaUri, $exception->schemaUri());
    }
}
