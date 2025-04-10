<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Fixture\SchemaNormalizer\NormalizeNormalizesJson;

use Ergebnis\Json\Json;
use Ergebnis\Json\Pointer;

/**
 * @psalm-immutable
 */
final class Scenario
{
    private Json $normalized;
    private Json $original;
    private Pointer\Specification $specificationForPointerToDataThatShouldNotBeSorted;
    private string $schemaUri;
    private string $key;

    private function __construct(
        string $key,
        string $schemaUri,
        Pointer\Specification $specificationForPointerToDataThatShouldNotBeSorted,
        Json $original,
        Json $normalized
    ) {
        $this->key = $key;
        $this->schemaUri = $schemaUri;
        $this->specificationForPointerToDataThatShouldNotBeSorted = $specificationForPointerToDataThatShouldNotBeSorted;
        $this->original = $original;
        $this->normalized = $normalized;
    }

    public static function create(
        string $key,
        string $schemaUri,
        Pointer\Specification $jsonPointerSpecification,
        Json $original,
        Json $normalized
    ): self {
        return new self(
            $key,
            $schemaUri,
            $jsonPointerSpecification,
            $original,
            $normalized,
        );
    }

    public function key(): string
    {
        return $this->key;
    }

    public function schemaUri(): string
    {
        return $this->schemaUri;
    }

    public function specificationForPointerToDataThatShouldNotBeSorted(): Pointer\Specification
    {
        return $this->specificationForPointerToDataThatShouldNotBeSorted;
    }

    public function original(): Json
    {
        return $this->original;
    }

    public function normalized(): Json
    {
        return $this->normalized;
    }
}
