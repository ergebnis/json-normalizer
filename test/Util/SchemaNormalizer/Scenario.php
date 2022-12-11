<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Util\SchemaNormalizer;

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
        private Json $normalized,
        private Json $original,
        private string $schemaUri,
        private Pointer\Specification $specificationForPointerToDataThatShouldNotBeSorted,
    ) {
    }

    public static function create(
        string $key,
        Json $normalized,
        Json $original,
        string $schemaUri,
        Pointer\Specification $jsonPointerSpecification,
    ): self {
        return new self(
            $key,
            $normalized,
            $original,
            $schemaUri,
            $jsonPointerSpecification,
        );
    }

    public function key(): string
    {
        return $this->key;
    }

    public function normalized(): Json
    {
        return $this->normalized;
    }

    public function original(): Json
    {
        return $this->original;
    }

    public function schemaUri(): string
    {
        return $this->schemaUri;
    }

    public function specificationForPointerToDataThatShouldNotBeSorted(): Pointer\Specification
    {
        return $this->specificationForPointerToDataThatShouldNotBeSorted;
    }
}
