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

namespace Ergebnis\Json\Normalizer\Rule\Prune;

use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Schema;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;

final class EmptyOptionalProperties implements Rule
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
        return Rule\Name::fromString('prune/empty-optional-properties');
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
            'Removes properties that the schema lists but does not require when their value is an empty array, an empty object, or null.',
            Rule\Example::create(
                <<<'JSON'
{
    "name": "ergebnis/json-normalizer",
    "keywords": [],
    "extra": {}
}

JSON,
                <<<'JSON'
{
    "name": "ergebnis/json-normalizer"
}

JSON,
            )->withSchema(<<<'JSON'
{
    "type": "object",
    "properties": {
        "name": {
            "type": "string"
        },
        "keywords": {
            "type": "array"
        },
        "extra": {
            "type": "object"
        }
    },
    "required": [
        "name"
    ]
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

        $listed = \array_flip($schema->propertyNames());

        $properties = $node->properties();
        $kept = [];

        foreach ($properties as $property) {
            $name = $property->name()->toString();

            if (
                \array_key_exists($name, $listed)
                && !$schema->isRequired($name)
                && self::isEmpty($property->value())
            ) {
                continue;
            }

            $kept[] = $property;
        }

        if (\count($kept) === \count($properties)) {
            return Rule\Action::keep();
        }

        return Rule\Action::replace(Parser\Node\ObjectNode::create(...$kept));
    }

    private static function isEmpty(Parser\Node\Node $value): bool
    {
        if ($value instanceof Parser\Node\NullNode) {
            return true;
        }

        if ($value instanceof Parser\Node\ArrayNode) {
            return 0 === $value->count();
        }

        if ($value instanceof Parser\Node\ObjectNode) {
            return 0 === $value->count();
        }

        return false;
    }
}
