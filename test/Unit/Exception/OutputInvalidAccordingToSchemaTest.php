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
 * @covers \Ergebnis\Json\Normalizer\Exception\OutputInvalidAccordingToSchema
 */
final class OutputInvalidAccordingToSchemaTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testWithErrorsReturnsOutputInvalidAccordingToSchema(): void
    {
        $faker = self::faker();

        $schemaUri = $faker->url();

        $errors = [
            $faker->sentence(),
            $faker->sentence(),
            $faker->sentence(),
        ];

        $exception = Exception\OutputInvalidAccordingToSchema::withErrors(
            $schemaUri,
            ...$errors,
        );

        $message = \sprintf(
            'Output JSON is not valid according to schema "%s".',
            $schemaUri,
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($schemaUri, $exception->schemaUri());
        self::assertSame($errors, $exception->errors());
    }
}
