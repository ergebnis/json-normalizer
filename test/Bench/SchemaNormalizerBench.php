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

namespace Ergebnis\Json\Normalizer\Test\Bench;

use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\SchemaNormalizer;
use Ergebnis\Json\Normalizer\Validator\SchemaValidator;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

final class SchemaNormalizerBench
{
    /**
     * @Revs(5)
     * @Iterations(1)
     */
    public function benchNormalizeProjectComposerFile(): void
    {
        self::normalize(
            __DIR__ . '/../../composer.json',
            self::localComposerSchema()
        );
    }

    /**
     * @see https://github.com/search?utf8=✓&q=repositories+filename%3Acomposer.json+size%3A%3E25000+path%3A%2F+&type=Code
     *
     * @Revs(5)
     * @Iterations(1)
     */
    public function benchNormalizeLargeComposerFile(): void
    {
        self::normalize(
            __DIR__ . '/../Fixture/LargeComposerFile/composer.json',
            self::localComposerSchema()
        );
    }

    private static function normalize(string $file, string $schemaUri): void
    {
        $encoded = \file_get_contents($file);

        if (!\is_string($encoded)) {
            throw new \RuntimeException(\sprintf(
                'File "%s" does not contain valid JSON.',
                $file
            ));
        }

        $json = Json::fromEncoded($encoded);

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            new SchemaStorage(),
            new SchemaValidator(new Validator())
        );

        $normalizer->normalize($json);
    }

    private static function localComposerSchema(): string
    {
        return \sprintf(
            'file://%s',
            __DIR__ . '/../Fixture/Vendor/Composer/composer-schema.json'
        );
    }
}
