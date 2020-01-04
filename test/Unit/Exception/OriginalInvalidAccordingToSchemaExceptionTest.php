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

use Ergebnis\Json\Normalizer\Exception\OriginalInvalidAccordingToSchemaException;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\OriginalInvalidAccordingToSchemaException
 */
final class OriginalInvalidAccordingToSchemaExceptionTest extends AbstractExceptionTestCase
{
    public function testFromSchemaUriReturnsOriginalInvalidAccordingToSchemaException(): void
    {
        $schemaUri = self::faker()->url;

        $exception = OriginalInvalidAccordingToSchemaException::fromSchemaUri($schemaUri);

        $message = \sprintf(
            'Original JSON is not valid according to schema "%s".',
            $schemaUri
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($schemaUri, $exception->schemaUri());
    }
}
