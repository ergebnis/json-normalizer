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

namespace Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Bin;

use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;

final class SortElements implements Rule
{
    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function name(): Rule\Name
    {
        return Rule\Name::fromString('vendor/composer/bin/sort-elements');
    }

    public function target(): Rule\Target
    {
        return Rule\Target::create(
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
            Parser\Node\ArrayNode::class,
        );
    }

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Sorts the elements of `bin` by value.',
            Rule\Example::create(
                <<<'JSON'
{
    "bin": [
        "bin/b",
        "bin/a"
    ]
}

JSON,
                <<<'JSON'
{
    "bin": [
        "bin/a",
        "bin/b"
    ]
}

JSON,
            ),
        );
    }

    public function apply(
        Parser\Node\Node $node,
        Context $context
    ): Rule\Action {
        if (!$node instanceof Parser\Node\ArrayNode) {
            return Rule\Action::keep();
        }

        $elements = $node->elements();
        $values = [];

        foreach ($elements as $element) {
            if (!$element instanceof Parser\Node\StringNode) {
                return Rule\Action::keep();
            }

            $values[] = $element->toString();
        }

        $indexes = \array_keys($elements);

        \usort($indexes, static function (int $one, int $two) use ($values): int {
            $comparison = \strcmp(
                $values[$one],
                $values[$two],
            );

            if (0 !== $comparison) {
                return $comparison;
            }

            return $one <=> $two;
        });

        if (\array_keys($elements) === $indexes) {
            return Rule\Action::keep();
        }

        $sorted = [];

        foreach ($indexes as $index) {
            $sorted[] = $elements[$index];
        }

        return Rule\Action::replace(Parser\Node\ArrayNode::create(...$sorted));
    }
}
