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
 * @internal
 */
final class SchemaResolver
{
    private SchemaLoader $loader;

    private function __construct(SchemaLoader $loader)
    {
        $this->loader = $loader;
    }

    public static function create(SchemaLoader $loader): self
    {
        return new self($loader);
    }

    public function resolve(
        Schema $schema,
        Parser\Node\Node $node
    ): Schema {
        $object = $schema->toObject();

        /**
         * @see https://json-schema.org/understanding-json-schema/reference/combining.html#anyof
         */
        if (
            \property_exists($object, 'anyOf')
            && \is_array($object->anyOf)
        ) {
            $branch = $this->firstMatchingBranch(
                \array_values($object->anyOf),
                $node,
            );

            if ($branch instanceof Schema) {
                return $this->resolve(
                    $branch,
                    $node,
                );
            }
        }

        /**
         * @see https://json-schema.org/understanding-json-schema/reference/combining.html#oneof
         */
        if (
            \property_exists($object, 'oneOf')
            && \is_array($object->oneOf)
        ) {
            $branch = $this->firstMatchingBranch(
                \array_values($object->oneOf),
                $node,
            );

            if ($branch instanceof Schema) {
                return $this->resolve(
                    $branch,
                    $node,
                );
            }
        }

        /**
         * @see https://json-schema.org/understanding-json-schema/structuring.html#reuse
         */
        if (
            \property_exists($object, '$ref')
            && \is_string($object->{'$ref'})
        ) {
            /** @var object $referenced */
            $referenced = $this->loader->storage()->resolveRefSchema($object);

            return $this->resolve(
                Schema::fromObject($referenced),
                $node,
            );
        }

        return $schema;
    }

    /**
     * @see https://json-schema.org/understanding-json-schema/reference/object.html#properties
     * @see https://json-schema.org/understanding-json-schema/reference/object.html#additional-properties
     */
    public function property(
        Schema $schema,
        string $name
    ): Schema {
        $object = $schema->toObject();

        if (
            \property_exists($object, 'properties')
            && \is_object($object->properties)
        ) {
            $properties = \get_object_vars($object->properties);

            if (
                \array_key_exists($name, $properties)
                && \is_object($properties[$name])
            ) {
                return Schema::fromObject($properties[$name]);
            }
        }

        if (
            \property_exists($object, 'additionalProperties')
            && \is_object($object->additionalProperties)
        ) {
            return Schema::fromObject($object->additionalProperties);
        }

        return Schema::fromObject(new \stdClass());
    }

    /**
     * @see https://json-schema.org/understanding-json-schema/reference/array.html#list-validation
     * @see https://json-schema.org/understanding-json-schema/reference/array.html#tuple-validation
     */
    public function element(
        Schema $schema,
        int $index
    ): Schema {
        $object = $schema->toObject();

        if (!\property_exists($object, 'items')) {
            return Schema::fromObject(new \stdClass());
        }

        if (\is_object($object->items)) {
            return Schema::fromObject($object->items);
        }

        if (
            \is_array($object->items)
            && \array_key_exists($index, $object->items)
            && \is_object($object->items[$index])
        ) {
            return Schema::fromObject($object->items[$index]);
        }

        return Schema::fromObject(new \stdClass());
    }

    /**
     * @param list<mixed> $branches
     */
    private function firstMatchingBranch(
        array $branches,
        Parser\Node\Node $node
    ): ?Schema {
        foreach ($branches as $branch) {
            if (!\is_object($branch)) {
                continue;
            }

            $schema = Schema::fromObject($branch);

            $errors = $this->loader->errors(
                $schema,
                $node,
            );

            if ([] === $errors) {
                return $schema;
            }
        }

        return null;
    }
}
