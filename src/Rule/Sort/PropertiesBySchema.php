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

namespace Ergebnis\Json\Normalizer\Rule\Sort;

use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Schema;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;

final class PropertiesBySchema implements Rule
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
        return Rule\Name::fromString('sort/properties-by-schema');
    }

    public function target(): Rule\Target
    {
        return Rule\Target::create(
            Pointer\Specification::always(),
            Parser\Node\ObjectNode::class,
        );
    }

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Sorts the properties of objects in the order in which the schema lists them, and keeps properties that the schema does not list after them, in their order.',
            Rule\Example::create(
                <<<'JSON'
{
    "license": "MIT",
    "type": "library",
    "name": "ergebnis/json-normalizer"
}

JSON,
                <<<'JSON'
{
    "name": "ergebnis/json-normalizer",
    "license": "MIT",
    "type": "library"
}

JSON,
            )->withSchema(<<<'JSON'
{
    "type": "object",
    "properties": {
        "name": {
            "type": "string"
        },
        "license": {
            "type": "string"
        }
    }
}

JSON),
        );
    }

    public function apply(
        Parser\Node\Node $node,
        Context $context
    ): Rule\Action {
        if (!$node instanceof Parser\Node\ObjectNode) {
            return Rule\Action::keep();
        }

        $schema = $context->schema();

        if (!$schema instanceof Schema) {
            return Rule\Action::keep();
        }

        $names = $schema->propertyNames();

        if ([] === $names) {
            return Rule\Action::keep();
        }

        $positions = \array_flip($names);
        $unlisted = \count($names);

        $properties = $node->properties();
        $ranks = [];

        foreach ($properties as $index => $property) {
            $name = $property->name()->toString();

            $ranks[$index] = $unlisted;

            if (\array_key_exists($name, $positions)) {
                $ranks[$index] = $positions[$name];
            }
        }

        $indexes = \array_keys($properties);

        \usort($indexes, static function (int $one, int $two) use ($ranks): int {
            $comparison = $ranks[$one] <=> $ranks[$two];

            if (0 !== $comparison) {
                return $comparison;
            }

            return $one <=> $two;
        });

        if (\array_keys($properties) === $indexes) {
            return Rule\Action::keep();
        }

        $sorted = [];

        foreach ($indexes as $index) {
            $sorted[] = $properties[$index];
        }

        return Rule\Action::replace(Parser\Node\ObjectNode::create(...$sorted));
    }
}
