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

namespace Ergebnis\Json\Normalizer\Test\Double\Set;

use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Set;
use Ergebnis\Json\Normalizer\Skip;

final class ListedSet implements Set
{
    private Set\Name $name;

    /**
     * @var list<Skip>
     */
    private array $skips;

    /**
     * @var list<Rule>
     */
    private array $rules;

    /**
     * @param list<Skip> $skips
     * @param list<Rule> $rules
     */
    private function __construct(
        Set\Name $name,
        array $skips,
        array $rules
    ) {
        $this->name = $name;
        $this->skips = $skips;
        $this->rules = $rules;
    }

    /**
     * @param list<Skip> $skips
     */
    public static function create(
        Set\Name $name,
        array $skips,
        Rule ...$rules
    ): self {
        return new self(
            $name,
            $skips,
            $rules,
        );
    }

    public function name(): Set\Name
    {
        return $this->name;
    }

    public function rules(): array
    {
        return $this->rules;
    }

    public function skips(): array
    {
        return $this->skips;
    }
}
