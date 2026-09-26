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

namespace Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Repositories;

use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;

final class SortFilterElements implements Rule
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
        return Rule\Name::fromString('vendor/composer/repositories/sort-filter-elements');
    }

    public function target(): Rule\Target
    {
        return Rule\Target::create(
            /**
             * @see https://getcomposer.org/doc/articles/repository-priorities.md#filtering-packages
             */
            Pointer\Specification::closure(static function (Pointer\JsonPointer $jsonPointer): bool {
                return 1 === \preg_match(
                    '{^/repositories/[^/]+/(exclude|only)$}',
                    $jsonPointer->toJsonString(),
                );
            }),
            Parser\Node\ArrayNode::class,
        );
    }

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Sorts the elements of `exclude` and `only` of repositories by value, with a wildcard after every other character, unless a value has a wildcard other than at its end.',
            Rule\Example::create(
                <<<'JSON'
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.example.org",
            "only": [
                "foo/*",
                "bar/baz",
                "foo/bar"
            ]
        }
    ]
}

JSON,
                <<<'JSON'
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.example.org",
            "only": [
                "bar/baz",
                "foo/bar",
                "foo/*"
            ]
        }
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

        foreach ($elements as $index => $element) {
            if (!$element instanceof Parser\Node\StringNode) {
                return Rule\Action::keep();
            }

            $value = $element->toString();

            if (Rule\Vendor\Composer\Wildcard::containsWildcardNotAtEnd($value)) {
                return Rule\Action::keep();
            }

            $values[$index] = $value;
        }

        $indexes = \array_keys($elements);

        \usort($indexes, static function (int $one, int $two) use ($values): int {
            $comparison = Rule\Vendor\Composer\Wildcard::compare(
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
