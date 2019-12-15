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

use Ergebnis\Json\Normalizer\Exception\SchemaUriCouldNotBeReadException;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\SchemaUriCouldNotBeReadException
 */
final class SchemaUriCouldNotBeReadExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsRuntimeException(): void
    {
        self::assertClassExtends(\RuntimeException::class, SchemaUriCouldNotBeReadException::class);
    }

    public function testFromSchemaUriReturnsSchemaUriCouldNotBeReadException(): void
    {
        $schemaUri = self::faker()->url;

        $exception = SchemaUriCouldNotBeReadException::fromSchemaUri($schemaUri);

        self::assertInstanceOf(SchemaUriCouldNotBeReadException::class, $exception);

        $message = \sprintf(
            'Schema URI "%s" does not reference a document that could be read.',
            $schemaUri
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($schemaUri, $exception->schemaUri());
    }
}
