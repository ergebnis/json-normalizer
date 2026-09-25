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

namespace Ergebnis\Json\Normalizer\Test\Fixture\Vendor\Composer\ComposerJsonNormalizer\NormalizeRejectsJson;

use Ergebnis\Json\Json;

/**
 * @psalm-immutable
 */
final class Scenario
{
    private Json $input;
    private string $key;

    private function __construct(
        string $key,
        Json $input
    ) {
        $this->key = $key;
        $this->input = $input;
    }

    public static function create(
        string $key,
        Json $input
    ): self {
        return new self(
            $key,
            $input,
        );
    }

    public function key(): string
    {
        return $this->key;
    }

    public function input(): Json
    {
        return $this->input;
    }
}
