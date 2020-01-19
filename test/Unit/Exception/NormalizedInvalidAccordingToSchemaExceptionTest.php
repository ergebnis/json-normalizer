<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Exception;

use Ergebnis\Json\Normalizer\Exception\NormalizedInvalidAccordingToSchemaException;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\NormalizedInvalidAccordingToSchemaException
 */
final class NormalizedInvalidAccordingToSchemaExceptionTest extends AbstractExceptionTestCase
{
    public function testFromSchemaUriReturnsNormalizedInvalidAccordingToSchemaException(): void
    {
        $schemaUri = self::faker()->url;

        $exception = NormalizedInvalidAccordingToSchemaException::fromSchemaUri($schemaUri);

        $message = \sprintf(
            'Normalized JSON is not valid according to schema "%s".',
            $schemaUri
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($schemaUri, $exception->schemaUri());
    }
}
