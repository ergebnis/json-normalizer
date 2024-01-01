<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2024 Andreas Möller
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

#[Framework\Attributes\CoversClass(Exception\SchemaUriCouldNotBeRead::class)]
final class SchemaUriCouldNotBeReadTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testDefaults(): void
    {
        $exception = new Exception\SchemaUriCouldNotBeRead();

        self::assertSame('', $exception->schemaUri());
    }

    public function testFromSchemaUriReturnsSchemaUriCouldNotBeReadException(): void
    {
        $schemaUri = self::faker()->url();

        $exception = Exception\SchemaUriCouldNotBeRead::fromSchemaUri($schemaUri);

        $message = \sprintf(
            'Schema URI "%s" does not reference a document that could be read.',
            $schemaUri,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($schemaUri, $exception->schemaUri());
    }
}
