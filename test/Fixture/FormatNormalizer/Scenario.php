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

namespace Ergebnis\Json\Normalizer\Test\Fixture\FormatNormalizer;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer;

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
        private Normalizer\Format\Format $format,
    ) {
    }

    public static function create(
        string $key,
        Json $normalized,
        Json $original,
        Normalizer\Format\Format $format,
    ): self {
        return new self(
            $key,
            $normalized,
            $original,
            $format,
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

    public function format(): Normalizer\Format\Format
    {
        return $this->format;
    }
}
