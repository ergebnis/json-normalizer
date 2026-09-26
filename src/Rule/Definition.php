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

use Ergebnis\Json\Normalizer\Exception;

/**
 * @psalm-immutable
 */
final class Definition
{
    private string $description;

    /**
     * @var list<Example>
     */
    private array $examples;

    /**
     * @param list<Example> $examples
     */
    private function __construct(
        string $description,
        array $examples
    ) {
        $this->description = $description;
        $this->examples = $examples;
    }

    /**
     * @throws Exception\InvalidRuleDefinition
     */
    public static function create(
        string $description,
        Example $example,
        Example ...$examples
    ): self {
        if ('' === \trim($description)) {
            throw Exception\InvalidRuleDefinition::blankDescription();
        }

        return new self(
            $description,
            \array_merge(
                [
                    $example,
                ],
                $examples,
            ),
        );
    }

    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return list<Example>
     */
    public function examples(): array
    {
        return $this->examples;
    }
}
