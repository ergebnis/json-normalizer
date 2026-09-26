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

use Ergebnis\Json\Pointer;

/**
 * @psalm-immutable
 */
final class Configuration
{
    /**
     * @var list<Set>
     */
    private array $sets;

    /**
     * @var list<Rule>
     */
    private array $rules;

    /**
     * @var list<Rule\Name>
     */
    private array $withoutRules;

    /**
     * @var array<string, list<Pointer\Specification>>
     */
    private array $skips;

    /**
     * @param list<Set>                                  $sets
     * @param list<Rule>                                 $rules
     * @param list<Rule\Name>                            $withoutRules
     * @param array<string, list<Pointer\Specification>> $skips
     */
    private function __construct(
        array $sets,
        array $rules,
        array $withoutRules,
        array $skips
    ) {
        $this->sets = $sets;
        $this->rules = $rules;
        $this->withoutRules = $withoutRules;
        $this->skips = $skips;
    }

    public static function create(): self
    {
        return new self(
            [],
            [],
            [],
            [],
        );
    }

    public function withSets(Set ...$sets): self
    {
        $skips = $this->skips;

        foreach ($sets as $set) {
            foreach ($set->skips() as $skip) {
                $skips[$skip->rule()->toString()][] = $skip->specification();
            }
        }

        return new self(
            \array_merge(
                $this->sets,
                $sets,
            ),
            $this->rules,
            $this->withoutRules,
            $skips,
        );
    }

    public function withRules(Rule ...$rules): self
    {
        return new self(
            $this->sets,
            \array_merge(
                $this->rules,
                $rules,
            ),
            $this->withoutRules,
            $this->skips,
        );
    }

    public function withoutRules(Rule\Name ...$names): self
    {
        return new self(
            $this->sets,
            $this->rules,
            \array_merge(
                $this->withoutRules,
                $names,
            ),
            $this->skips,
        );
    }

    public function withSkip(
        Rule\Name $name,
        Pointer\Specification $specification
    ): self {
        $skips = $this->skips;

        $skips[$name->toString()][] = $specification;

        return new self(
            $this->sets,
            $this->rules,
            $this->withoutRules,
            $skips,
        );
    }

    /**
     * @return list<Rule>
     */
    public function rules(): array
    {
        /** @var array<string, Rule> $rules */
        $rules = [];

        foreach ($this->sets as $set) {
            foreach ($set->rules() as $rule) {
                $rules[$rule->name()->toString()] = $rule;
            }
        }

        foreach ($this->rules as $rule) {
            $rules[$rule->name()->toString()] = $rule;
        }

        foreach ($this->withoutRules as $name) {
            unset($rules[$name->toString()]);
        }

        return \array_values($rules);
    }

    public function skipFor(Rule\Name $name): Pointer\Specification
    {
        if (!\array_key_exists($name->toString(), $this->skips)) {
            return Pointer\Specification::never();
        }

        $specifications = $this->skips[$name->toString()];

        if (1 === \count($specifications)) {
            return $specifications[0];
        }

        return Pointer\Specification::anyOf(...$specifications);
    }
}
