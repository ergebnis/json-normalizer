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

use Localheinz\Json\Normalizer\Exception\OriginalInvalidAccordingToSchemaException;

/**
 * @internal
 */
final class OriginalInvalidAccordingToSchemaExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsRuntimeException(): void
    {
        $this->assertClassExtends(\RuntimeException::class, OriginalInvalidAccordingToSchemaException::class);
    }

    public function testFromSchemaUriReturnsOriginalInvalidAccordingToSchemaException(): void
    {
        $schemaUri = $this->faker()->url;

        $exception = OriginalInvalidAccordingToSchemaException::fromSchemaUri($schemaUri);

        $this->assertInstanceOf(OriginalInvalidAccordingToSchemaException::class, $exception);

        $message = \sprintf(
            'Original JSON is not valid according to schema "%s".',
            $schemaUri
        );

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($schemaUri, $exception->schemaUri());
    }
}
