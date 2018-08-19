<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit\Exception;

use Localheinz\Json\Normalizer\Exception\SchemaUriCouldNotBeResolvedException;

/**
 * @internal
 */
final class SchemaUriCouldNotBeResolvedExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsRuntimeException(): void
    {
        $this->assertClassExtends(\RuntimeException::class, SchemaUriCouldNotBeResolvedException::class);
    }

    public function testFromSchemaUriReturnsSchemaUriCouldNotBeResolvedException(): void
    {
        $schemaUri = $this->faker()->url;

        $exception = SchemaUriCouldNotBeResolvedException::fromSchemaUri($schemaUri);

        $this->assertInstanceOf(SchemaUriCouldNotBeResolvedException::class, $exception);

        $message = \sprintf(
            'Schema URI "%s" could not be resolved.',
            $schemaUri
        );

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($schemaUri, $exception->schemaUri());
    }
}
