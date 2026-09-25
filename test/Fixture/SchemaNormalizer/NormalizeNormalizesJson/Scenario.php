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

namespace Ergebnis\Json\Normalizer\Test\Fixture\SchemaNormalizer\NormalizeNormalizesJson;

use Ergebnis\Json\Json;
use Ergebnis\Json\Pointer;

/**
 * @psalm-immutable
 */
final class Scenario
{
    private Json $input;
    private Json $output;
    private Pointer\Specification $specificationForPointerToDataThatShouldNotBeSorted;
    private string $schemaUri;
    private string $key;

    private function __construct(
        string $key,
        string $schemaUri,
        Pointer\Specification $specificationForPointerToDataThatShouldNotBeSorted,
        Json $input,
        Json $output
    ) {
        $this->key = $key;
        $this->schemaUri = $schemaUri;
        $this->specificationForPointerToDataThatShouldNotBeSorted = $specificationForPointerToDataThatShouldNotBeSorted;
        $this->input = $input;
        $this->output = $output;
    }

    public static function create(
        string $key,
        string $schemaUri,
        Pointer\Specification $jsonPointerSpecification,
        Json $input,
        Json $output
    ): self {
        return new self(
            $key,
            $schemaUri,
            $jsonPointerSpecification,
            $input,
            $output,
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

    public function input(): Json
    {
        return $this->input;
    }

    public function output(): Json
    {
        return $this->output;
    }
}
