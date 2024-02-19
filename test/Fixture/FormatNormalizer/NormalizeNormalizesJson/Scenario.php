<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2024 Andreas Möller
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
    private Json $normalized;
    private Json $original;
    private Normalizer\Format\Format $format;
    private string $key;

    private function __construct(
        string $key,
        Normalizer\Format\Format $format,
        Json $original,
        Json $normalized
    ) {
        $this->key = $key;
        $this->format = $format;
        $this->original = $original;
        $this->normalized = $normalized;
    }

    public static function create(
        string $key,
        Normalizer\Format\Format $format,
        Json $original,
        Json $normalized
    ): self {
        return new self(
            $key,
            $format,
            $original,
            $normalized,
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

    public function original(): Json
    {
        return $this->original;
    }

    public function normalized(): Json
    {
        return $this->normalized;
    }
}
