<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Exception;

use Ergebnis\Json\Normalizer\Exception;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\SchemaUriReferencesDocumentWithInvalidMediaType
 */
final class SchemaUriReferencesDocumentWithInvalidMediaTypeTest extends AbstractExceptionTestCase
{
    public function testDefaults(): void
    {
        $exception = new Exception\SchemaUriReferencesDocumentWithInvalidMediaType();

        self::assertSame('', $exception->schemaUri());
    }

    public function testFromSchemaUriReturnsSchemaUriReferencesDocumentWithInvalidMediaType(): void
    {
        $schemaUri = self::faker()->url();

        $exception = Exception\SchemaUriReferencesDocumentWithInvalidMediaType::fromSchemaUri($schemaUri);

        $message = \sprintf(
            'Schema URI "%s" does not reference a document with media type "application/schema+json".',
            $schemaUri,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($schemaUri, $exception->schemaUri());
    }
}
