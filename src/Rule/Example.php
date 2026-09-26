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

namespace Ergebnis\Json\Normalizer\Rule;

/**
 * @psalm-immutable
 */
final class Example
{
    private string $input;
    private string $output;
    private ?string $schema;

    private function __construct(
        string $input,
        string $output,
        ?string $schema
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->schema = $schema;
    }

    public static function create(
        string $input,
        string $output
    ): self {
        return new self(
            $input,
            $output,
            null,
        );
    }

    public function withSchema(string $schema): self
    {
        return new self(
            $this->input,
            $this->output,
            $schema,
        );
    }

    public function input(): string
    {
        return $this->input;
    }

    public function output(): string
    {
        return $this->output;
    }

    public function schema(): ?string
    {
        return $this->schema;
    }
}
