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

use Ergebnis\Json\Normalizer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Exception\NormalizedInvalidAccordingToSchema
 */
final class NormalizedInvalidAccordingToSchemaTest extends Framework\TestCase
{
    use Normalizer\Test\Util\Helper;

    public function testDefaults(): void
    {
        $exception = new Normalizer\Exception\NormalizedInvalidAccordingToSchema();

        self::assertSame('', $exception->schemaUri());
        self::assertSame([], $exception->errors());
    }

    public function testFromSchemaUriReturnsNormalizedInvalidAccordingToSchemaException(): void
    {
        $faker = self::faker();

        $schemaUri = $faker->url();

        $errors = [
            $faker->sentence(),
            $faker->sentence(),
            $faker->sentence(),
        ];

        $exception = Normalizer\Exception\NormalizedInvalidAccordingToSchema::fromSchemaUriAndErrors(
            $schemaUri,
            ...$errors,
        );

        $message = \sprintf(
            'Normalized JSON is not valid according to schema "%s".',
            $schemaUri,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($schemaUri, $exception->schemaUri());
        self::assertSame($errors, $exception->errors());
    }
}
