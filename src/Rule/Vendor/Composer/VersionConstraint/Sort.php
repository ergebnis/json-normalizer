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

namespace Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint;

use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;

final class Sort implements Rule
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
        return Rule\Name::fromString('vendor/composer/version-constraint/sort');
    }

    public function target(): Rule\Target
    {
        return Constraints::target();
    }

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Sorts or-constraints and and-constraints in version constraints by version.',
            Rule\Example::create(
                <<<'JSON'
{
    "require": {
        "ergebnis/json-normalizer": "^5.0 || ^4.0"
    }
}

JSON,
                <<<'JSON'
{
    "require": {
        "ergebnis/json-normalizer": "^4.0 || ^5.0"
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
        if (!$node instanceof Parser\Node\StringNode) {
            return Rule\Action::keep();
        }

        $versionConstraint = $node->toString();

        if (!Constraints::isParsable($versionConstraint)) {
            return Rule\Action::keep();
        }

        $normalized = self::sort($versionConstraint);

        if ($normalized === $versionConstraint) {
            return Rule\Action::keep();
        }

        return Rule\Action::replace(Parser\Node\StringNode::fromString($normalized));
    }

    private static function sort(string $versionConstraint): string
    {
        $orConstraints = [];

        foreach (Constraints::splitIntoOrConstraints($versionConstraint) as $orConstraint) {
            $orConstraints[] = Constraints::joinAndConstraints(...self::sorted(Constraints::splitIntoAndConstraints($orConstraint)));
        }

        return Constraints::joinOrConstraints(...self::sorted($orConstraints));
    }

    /**
     * @param list<string> $constraints
     *
     * @return list<string>
     */
    private static function sorted(array $constraints): array
    {
        $versions = [];

        foreach ($constraints as $index => $constraint) {
            $versions[$index] = \trim(
                $constraint,
                '<>=!~^',
            );
        }

        $indexes = \array_keys($constraints);

        \usort($indexes, static function (int $one, int $two) use ($versions): int {
            $comparison = \strnatcmp(
                $versions[$one],
                $versions[$two],
            );

            if (0 !== $comparison) {
                return $comparison;
            }

            return $one <=> $two;
        });

        $sorted = [];

        foreach ($indexes as $index) {
            $sorted[] = $constraints[$index];
        }

        return $sorted;
    }
}
