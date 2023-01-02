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

namespace Ergebnis\Json\Normalizer\Test\Fixture\SchemaNormalizer\NormalizeNormalizesJson;

use Ergebnis\Json\Json;
use Ergebnis\Json\Pointer;

/**
 * @internal
 *
 * @psalm-immutable
 */
final class Scenario
{
    private function __construct(
        private string $key,
        private string $schemaUri,
        private Pointer\Specification $specificationForPointerToDataThatShouldNotBeSorted,
        private Json $original,
        private Json $normalized,
    ) {
    }

    public static function create(
        string $key,
        string $schemaUri,
        Pointer\Specification $jsonPointerSpecification,
        Json $original,
        Json $normalized,
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
