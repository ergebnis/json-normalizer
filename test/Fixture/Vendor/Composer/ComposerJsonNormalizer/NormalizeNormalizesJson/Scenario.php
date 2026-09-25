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
    private Json $input;
    private Json $output;
    private string $key;

    private function __construct(
        string $key,
        Json $input,
        Json $output
    ) {
        $this->key = $key;
        $this->input = $input;
        $this->output = $output;
    }

    public static function create(
        string $key,
        Json $input,
        Json $output
    ): self {
        return new self(
            $key,
            $input,
            $output,
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

    public function output(): Json
    {
        return $this->output;
    }
}
