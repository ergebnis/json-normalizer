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

final class SortProperties implements Rule
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
        return Rule\Name::fromString('vendor/composer/config/sort-properties');
    }

    public function target(): Rule\Target
    {
        return Rule\Target::create(
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/config')),
            Parser\Node\ObjectNode::class,
        );
    }

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Sorts the properties of `config` by name.',
            Rule\Example::create(
                <<<'JSON'
{
    "config": {
        "sort-packages": true,
        "platform": {
            "php": "7.4.33"
        },
        "allow-plugins": {
            "ergebnis/composer-normalize": true
        }
    }
}

JSON,
                <<<'JSON'
{
    "config": {
        "allow-plugins": {
            "ergebnis/composer-normalize": true
        },
        "platform": {
            "php": "7.4.33"
        },
        "sort-packages": true
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

        $indexes = \array_keys($properties);

        \usort($indexes, static function (int $one, int $two) use ($names): int {
            $comparison = \strcmp(
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
