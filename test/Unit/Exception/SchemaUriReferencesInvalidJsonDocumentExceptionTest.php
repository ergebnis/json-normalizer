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

use Localheinz\Json\Normalizer\Exception\SchemaUriReferencesInvalidJsonDocumentException;

/**
 * @internal
 * @coversNothing
 */
final class SchemaUriReferencesInvalidJsonDocumentExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsRuntimeException(): void
    {
        $this->assertClassExtends(\RuntimeException::class, SchemaUriReferencesInvalidJsonDocumentException::class);
    }

    public function testFromSchemaUriReturnsSchemaUriReferencesDocumentWithInvalidMediaType(): void
    {
        $schemaUri = $this->faker()->url;

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
