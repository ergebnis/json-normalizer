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

use Ergebnis\Json\Normalizer\Exception\SchemaUriReferencesInvalidJsonDocumentException;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\SchemaUriReferencesInvalidJsonDocumentException
 */
final class SchemaUriReferencesInvalidJsonDocumentExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsRuntimeException(): void
    {
        self::assertClassExtends(\RuntimeException::class, SchemaUriReferencesInvalidJsonDocumentException::class);
    }

    public function testFromSchemaUriReturnsSchemaUriReferencesDocumentWithInvalidMediaType(): void
    {
        $schemaUri = self::faker()->url;

        $exception = SchemaUriReferencesInvalidJsonDocumentException::fromSchemaUri($schemaUri);

        self::assertInstanceOf(SchemaUriReferencesInvalidJsonDocumentException::class, $exception);

        $message = \sprintf(
            'Schema URI "%s" does not reference a document with valid JSON syntax.',
            $schemaUri
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($schemaUri, $exception->schemaUri());
    }
}
