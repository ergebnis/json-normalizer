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

namespace Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Packages;

use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;

final class MergeDuplicateExtensions implements Rule
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
        return Rule\Name::fromString('vendor/composer/packages/merge-duplicate-extensions');
    }

    public function target(): Rule\Target
    {
        return Rule\Target::create(
            Pointer\Specification::anyOf(
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/conflict')),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/provide')),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/replace')),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/require')),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/require-dev')),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/suggest')),
            ),
            Parser\Node\ObjectNode::class,
        );
    }

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Renames extensions in package links to lower case with spaces replaced by hyphens, and merges the version constraints of extensions that then have the same name.',
            Rule\Example::create(
                <<<'JSON'
{
    "require": {
        "ext-json": "^1.0",
        "php": "^8.0",
        "ext-JSON": "^2.0"
    }
}

JSON,
                <<<'JSON'
{
    "require": {
        "ext-json": "^2.0||^1.0",
        "php": "^8.0"
    }
}

JSON,
            ),
        );
    }

    /**
     * This code is adopted from composer/composer (originally licensed under MIT by Nils Adermann <naderman@naderman.de>
     * and Jordi Boggiano <j.boggiano@seld.be>).
     *
     * @see https://github.com/composer/composer/blob/2.8.1/src/Composer/Repository/PlatformRepository.php#L682
     */
    public function apply(
        Parser\Node\Node $node,
        Context $context
    ): Rule\Action {
        if (!$node instanceof Parser\Node\ObjectNode) {
            return Rule\Action::keep();
        }

        $original = $node->properties();
        $properties = $original;

        foreach ($original as $property) {
            $name = $property->name()->toString();

            if (
                5 > \strlen($name)
                || 'ext-' !== \strtolower(\substr($name, 0, 4))
            ) {
                continue;
            }

            $newName = \str_replace(
                ' ',
                '-',
                \strtolower($name),
            );

            if ($name === $newName) {
                continue;
            }

            $value = $property->value();

            if (!$value instanceof Parser\Node\StringNode) {
                continue;
            }

            $existingIndex = self::indexOf(
                $properties,
                $newName,
            );

            if (null === $existingIndex) {
                $properties = self::without(
                    $properties,
                    $property,
                );

                $properties[] = Parser\Node\ObjectProperty::create(
                    Parser\Node\StringNode::fromString($newName),
                    $value,
                );

                continue;
            }

            $existing = $properties[$existingIndex];

            $existingValue = $existing->value();

            if (!$existingValue instanceof Parser\Node\StringNode) {
                continue;
            }

            $properties[$existingIndex] = Parser\Node\ObjectProperty::create(
                $existing->name(),
                Parser\Node\StringNode::fromString(\sprintf(
                    '%s||%s',
                    $value->toString(),
                    $existingValue->toString(),
                )),
            );

            $properties = self::without(
                $properties,
                $property,
            );
        }

        if ($properties === $original) {
            return Rule\Action::keep();
        }

        return Rule\Action::replace(Parser\Node\ObjectNode::create(...$properties));
    }

    /**
     * @param array<int, Parser\Node\ObjectProperty> $properties
     */
    private static function indexOf(
        array $properties,
        string $name
    ): ?int {
        foreach ($properties as $index => $property) {
            if ($property->name()->toString() === $name) {
                return $index;
            }
        }

        return null;
    }

    /**
     * @param array<int, Parser\Node\ObjectProperty> $properties
     *
     * @return list<Parser\Node\ObjectProperty>
     */
    private static function without(
        array $properties,
        Parser\Node\ObjectProperty $property
    ): array {
        return \array_values(\array_filter($properties, static function (Parser\Node\ObjectProperty $candidate) use ($property): bool {
            return $candidate !== $property;
        }));
    }
}
