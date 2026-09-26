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

final class PropertiesByName implements Rule
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
        return Rule\Name::fromString('sort/properties-by-name');
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
            'Sorts the properties of objects that the schema does not list by name, after the properties that it lists.',
            Rule\Example::create(
                <<<'JSON'
{
    "name": "ergebnis/json-normalizer",
    "type": "library",
    "license": "MIT"
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

        $listed = [];

        $schema = $context->schema();

        if ($schema instanceof Schema) {
            $listed = \array_flip($schema->propertyNames());
        }

        $properties = $node->properties();

        $listedIndexes = [];
        $unlistedIndexes = [];
        $names = [];

        foreach ($properties as $index => $property) {
            $name = $property->name()->toString();

            if (\array_key_exists($name, $listed)) {
                $listedIndexes[] = $index;

                continue;
            }

            $unlistedIndexes[] = $index;
            $names[$index] = $name;
        }

        \usort($unlistedIndexes, static function (int $one, int $two) use ($names): int {
            $comparison = \strcmp(
                $names[$one],
                $names[$two],
            );

            if (0 !== $comparison) {
                return $comparison;
            }

            return $one <=> $two;
        });

        $indexes = \array_merge(
            $listedIndexes,
            $unlistedIndexes,
        );

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
