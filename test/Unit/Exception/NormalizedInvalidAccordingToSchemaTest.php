<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
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

#[Framework\Attributes\CoversClass(Exception\NormalizedInvalidAccordingToSchema::class)]
final class NormalizedInvalidAccordingToSchemaTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testDefaults(): void
    {
        $exception = new Exception\NormalizedInvalidAccordingToSchema();

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

        $exception = Exception\NormalizedInvalidAccordingToSchema::fromSchemaUriAndErrors(
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
