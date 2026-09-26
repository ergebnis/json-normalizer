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

/**
 * @psalm-immutable
 */
final class Schema
{
    private object $schema;

    private function __construct(object $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @internal
     */
    public static function fromObject(object $schema): self
    {
        return new self($schema);
    }

    /**
     * @internal
     */
    public function toObject(): object
    {
        return $this->schema;
    }

    /**
     * @return list<string>
     */
    public function propertyNames(): array
    {
        if (!\property_exists($this->schema, 'properties')) {
            return [];
        }

        if (!\is_object($this->schema->properties)) {
            return [];
        }

        $names = [];

        foreach (\get_object_vars($this->schema->properties) as $name => $schema) {
            $names[] = (string) $name;
        }

        return $names;
    }

    public function isRequired(string $name): bool
    {
        if (!\property_exists($this->schema, 'required')) {
            return false;
        }

        if (!\is_array($this->schema->required)) {
            return false;
        }

        return \in_array(
            $name,
            $this->schema->required,
            true,
        );
    }
}
