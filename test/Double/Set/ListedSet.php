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

final class ListedSet implements Set
{
    private Set\Name $name;

    /**
     * @var list<Rule>
     */
    private array $rules;

    /**
     * @param list<Rule> $rules
     */
    private function __construct(
        Set\Name $name,
        array $rules
    ) {
        $this->name = $name;
        $this->rules = $rules;
    }

    public static function create(
        Set\Name $name,
        Rule ...$rules
    ): self {
        return new self(
            $name,
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
}
