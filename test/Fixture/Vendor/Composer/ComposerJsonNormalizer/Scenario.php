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

namespace Ergebnis\Json\Normalizer\Test\Fixture\Vendor\Composer\ComposerJsonNormalizer;

use Ergebnis\Json\Json;

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
    ) {
    }

    public static function create(
        string $key,
        Json $normalized,
        Json $original,
    ): self {
        return new self(
            $key,
            $normalized,
            $original,
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
}
