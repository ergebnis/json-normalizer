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

namespace Ergebnis\Json\Normalizer;

use Ergebnis\Json\Parser;

/**
 * @psalm-immutable
 */
final class Result
{
    private Parser\Raw $input;
    private Parser\Raw $output;

    /**
     * @var list<Change>
     */
    private array $changes;

    /**
     * @param list<Change> $changes
     */
    private function __construct(
        Parser\Raw $input,
        Parser\Raw $output,
        array $changes
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->changes = $changes;
    }

    public static function create(
        Parser\Raw $input,
        Parser\Raw $output,
        Change ...$changes
    ): self {
        return new self(
            $input,
            $output,
            $changes,
        );
    }

    public function input(): Parser\Raw
    {
        return $this->input;
    }

    public function output(): Parser\Raw
    {
        return $this->output;
    }

    /**
     * @return list<Change>
     */
    public function changes(): array
    {
        return $this->changes;
    }

    public function isChanged(): bool
    {
        return $this->input->toString() !== $this->output->toString();
    }
}
