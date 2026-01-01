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

namespace Ergebnis\Json\Normalizer\Test\Fixture\Vendor\Composer\ComposerJsonNormalizer\NormalizeNormalizesJson;

use Ergebnis\Json\Json;

/**
 * @psalm-immutable
 */
final class Scenario
{
    private Json $normalized;
    private Json $original;
    private string $key;

    private function __construct(
        string $key,
        Json $original,
        Json $normalized
    ) {
        $this->key = $key;
        $this->original = $original;
        $this->normalized = $normalized;
    }

    public static function create(
        string $key,
        Json $original,
        Json $normalized
    ): self {
        return new self(
            $key,
            $original,
            $normalized,
        );
    }

    public function key(): string
    {
        return $this->key;
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
