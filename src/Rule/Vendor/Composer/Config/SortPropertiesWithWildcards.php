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

namespace Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Config;

use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;

final class SortPropertiesWithWildcards implements Rule
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
        return Rule\Name::fromString('vendor/composer/config/sort-properties-with-wildcards');
    }

    public function target(): Rule\Target
    {
        return Rule\Target::create(
            Pointer\Specification::anyOf(
                /**
                 * @see https://getcomposer.org/doc/06-config.md#allow-plugins
                 */
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/config/allow-plugins')),
                /**
                 * @see https://getcomposer.org/doc/06-config.md#preferred-install
                 */
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/config/preferred-install')),
            ),
            Parser\Node\ObjectNode::class,
        );
    }

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Sorts the properties of `config.allow-plugins` and `config.preferred-install` by name, with a wildcard after every other character, unless a name has a wildcard other than at its end.',
            Rule\Example::create(
                <<<'JSON'
{
    "config": {
        "allow-plugins": {
            "foo/*": true,
            "bar/baz": true,
            "foo/bar": false
        }
    }
}

JSON,
                <<<'JSON'
{
    "config": {
        "allow-plugins": {
            "bar/baz": true,
            "foo/bar": false,
            "foo/*": true
        }
    }
}

JSON,
            ),
        );
    }

    public function apply(
        Parser\Node\Node $node,
        Context $context
    ): Rule\Action {
        if (!$node instanceof Parser\Node\ObjectNode) {
            return Rule\Action::keep();
        }

        $properties = $node->properties();
        $names = [];

        foreach ($properties as $index => $property) {
            $names[$index] = $property->name()->toString();
        }

        foreach ($names as $name) {
            if (Rule\Vendor\Composer\Wildcard::containsWildcardNotAtEnd($name)) {
                return Rule\Action::keep();
            }
        }

        $indexes = \array_keys($properties);

        \usort($indexes, static function (int $one, int $two) use ($names): int {
            $comparison = Rule\Vendor\Composer\Wildcard::compare(
                $names[$one],
                $names[$two],
            );

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
