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

use Composer\Semver;
use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;

final class RemoveOverlapping implements Rule
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
        return Rule\Name::fromString('vendor/composer/version-constraint/remove-overlapping');
    }

    public function target(): Rule\Target
    {
        return Constraints::target();
    }

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Removes or-constraints that other or-constraints with a caret or a tilde already cover from version constraints.',
            Rule\Example::create(
                <<<'JSON'
{
    "require": {
        "ergebnis/json-normalizer": "^4.0 || ^4.1"
    }
}

JSON,
                <<<'JSON'
{
    "require": {
        "ergebnis/json-normalizer": "^4.0"
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

        $normalized = self::removeOverlapping($versionConstraint);

        if ($normalized === $versionConstraint) {
            return Rule\Action::keep();
        }

        return Rule\Action::replace(Parser\Node\StringNode::fromString($normalized));
    }

    private static function removeOverlapping(string $versionConstraint): string
    {
        $orConstraints = Constraints::splitIntoOrConstraints($versionConstraint);

        $regex = '{^[~^]?\d+(?:\.\d+)*$}';

        $count = \count($orConstraints);

        for ($i = 0; $i < $count; ++$i) {
            $a = $orConstraints[$i];

            if (!\is_string($a)) {
                continue;
            }

            if ('*' === $a) {
                return $a;
            }

            if (1 !== \preg_match($regex, $a)) {
                continue;
            }

            for ($j = $i + 1; $j < $count; ++$j) {
                $b = $orConstraints[$j];

                if (!\is_string($b)) {
                    continue;
                }

                if (1 !== \preg_match($regex, $b)) {
                    continue;
                }

                if (
                    !Semver\Semver::satisfies(\ltrim($a, '^~'), $b)
                    && !Semver\Semver::satisfies(\ltrim($b, '^~'), $a)
                ) {
                    continue;
                }

                if ('^' === $a[0]) {
                    $orConstraints[$j] = null;
                } elseif ('^' === $b[0]) {
                    $orConstraints[$i] = null;
                } elseif ('~' === $a[0]) {
                    $orConstraints[$j] = null;
                } elseif ('~' === $b[0]) {
                    $orConstraints[$i] = null;
                }
            }
        }

        $remaining = [];

        foreach ($orConstraints as $orConstraint) {
            if (!\is_string($orConstraint)) {
                continue;
            }

            $remaining[] = $orConstraint;
        }

        return Constraints::joinOrConstraints(...$remaining);
    }
}
