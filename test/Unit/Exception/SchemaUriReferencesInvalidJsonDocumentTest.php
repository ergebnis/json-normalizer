<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Exception;

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Exception\SchemaUriReferencesInvalidJsonDocument
 */
final class SchemaUriReferencesInvalidJsonDocumentTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromStringReturnsSchemaUriReferencesInvalidJsonDocument(): void
    {
        $value = self::faker()->url();

        $exception = Exception\SchemaUriReferencesInvalidJsonDocument::fromString($value);

        $message = \sprintf(
            'Schema URI "%s" does not reference a document with valid JSON syntax.',
            $value,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($value, $exception->value());
    }
}
