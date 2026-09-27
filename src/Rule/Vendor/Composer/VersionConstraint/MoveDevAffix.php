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

final class MoveDevAffix implements Rule
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
        return Rule\Name::fromString('vendor/composer/version-constraint/move-dev-affix');
    }

    public function target(): Rule\Target
    {
        return Constraints::target();
    }

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Moves `dev` to the end of numeric branch names and to the start of other branch names in version constraints.',
            Rule\Example::create(
                <<<'JSON'
{
    "require": {
        "ergebnis/json-normalizer": "main-dev"
    }
}

JSON,
                <<<'JSON'
{
    "require": {
        "ergebnis/json-normalizer": "dev-main"
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

        $normalized = self::moveDevAffix($versionConstraint);

        if ($normalized === $versionConstraint) {
            return Rule\Action::keep();
        }

        return Rule\Action::replace(Parser\Node\StringNode::fromString($normalized));
    }

    /**
     * @see https://github.com/composer/semver/blob/3.4.0/src/VersionParser.php#L216
     */
    private static function moveDevAffix(string $versionConstraint): string
    {
        $parts = \explode(
            ' ',
            $versionConstraint,
        );

        $moved = [];

        foreach ($parts as $part) {
            $moved[] = self::moveDevAffixOfPart($part);
        }

        return \implode(
            ' ',
            $moved,
        );
    }

    private static function moveDevAffixOfPart(string $part): string
    {
        if (4 >= \strlen($part)) {
            return $part;
        }

        if (0 === \strpos($part, 'dev-')) {
            $branch = \substr(
                $part,
                4,
            );
        } elseif ('-dev' === \substr($part, -4)) {
            $branch = \substr(
                $part,
                0,
                -4,
            );
        } else {
            return $part;
        }

        if (1 === \preg_match('{^v?\d+(\.(?:\d+|[xX*]))?(\.(?:\d+|[xX*]))?(\.(?:\d+|[xX*]))?$}i', $branch)) {
            return \sprintf(
                '%s-dev',
                $branch,
            );
        }

        return \sprintf(
            'dev-%s',
            $branch,
        );
    }
}
