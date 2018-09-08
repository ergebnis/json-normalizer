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

use Localheinz\Json\Normalizer\Exception\NormalizedInvalidAccordingToSchemaException;

/**
 * @internal
 */
final class NormalizedInvalidAccordingToSchemaExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsRuntimeException(): void
    {
        $this->assertClassExtends(\RuntimeException::class, NormalizedInvalidAccordingToSchemaException::class);
    }

    public function testFromSchemaUriReturnsNormalizedInvalidAccordingToSchemaException(): void
    {
        $schemaUri = $this->faker()->url;

        $exception = NormalizedInvalidAccordingToSchemaException::fromSchemaUri($schemaUri);

        $this->assertInstanceOf(NormalizedInvalidAccordingToSchemaException::class, $exception);

        $message = \sprintf(
            'Normalized JSON is not valid according to schema "%s".',
            $schemaUri
        );

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($schemaUri, $exception->schemaUri());
    }
}
