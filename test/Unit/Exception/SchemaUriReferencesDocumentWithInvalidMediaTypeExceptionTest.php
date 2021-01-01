<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Exception;

use Ergebnis\Json\Normalizer\Exception\SchemaUriReferencesDocumentWithInvalidMediaTypeException;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\SchemaUriReferencesDocumentWithInvalidMediaTypeException
 */
final class SchemaUriReferencesDocumentWithInvalidMediaTypeExceptionTest extends AbstractExceptionTestCase
{
    public function testDefaults(): void
    {
        $exception = new SchemaUriReferencesDocumentWithInvalidMediaTypeException();

        self::assertSame('', $exception->schemaUri());
    }

    public function testFromSchemaUriReturnsSchemaUriReferencesDocumentWithInvalidMediaType(): void
    {
        $schemaUri = self::faker()->url;

        $exception = SchemaUriReferencesDocumentWithInvalidMediaTypeException::fromSchemaUri($schemaUri);

        $message = \sprintf(
            'Schema URI "%s" does not reference a document with media type "application/schema+json".',
            $schemaUri
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($schemaUri, $exception->schemaUri());
    }
}
