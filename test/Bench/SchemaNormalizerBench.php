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

namespace Localheinz\Json\Normalizer\Test\Bench;

use Localheinz\Json\Normalizer\SchemaNormalizer;
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
        $this->normalize(
            __DIR__ . '/../../composer.json',
            $this->localComposerSchema()
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
        $this->normalize(
            __DIR__ . '/../Fixture/LargeComposerFile/composer.json',
            $this->localComposerSchema()
        );
    }

    private function normalize(string $file, string $schemaUri): void
    {
        $original = \file_get_contents($file);

        $normalizer = new SchemaNormalizer($schemaUri);

        $normalizer->normalize($original);
    }

    private function localComposerSchema(): string
    {
        return \sprintf(
            'file://%s',
            __DIR__ . '/../Fixture/composer-schema.json'
        );
    }
}
