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

namespace Ergebnis\Json\Normalizer\Test\Fixture\FormatNormalizer\NormalizeNormalizesJson;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer;

/**
 * @psalm-immutable
 */
final class Scenario
{
    private Json $input;
    private Json $output;
    private Normalizer\Format\Format $format;
    private string $key;

    private function __construct(
        string $key,
        Normalizer\Format\Format $format,
        Json $input,
        Json $output
    ) {
        $this->key = $key;
        $this->format = $format;
        $this->input = $input;
        $this->output = $output;
    }

    public static function create(
        string $key,
        Normalizer\Format\Format $format,
        Json $input,
        Json $output
    ): self {
        return new self(
            $key,
            $format,
            $input,
            $output,
        );
    }

    public function key(): string
    {
        return $this->key;
    }

    public function format(): Normalizer\Format\Format
    {
        return $this->format;
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
