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

final class ReplaceWildcardWithTilde implements Rule
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
        return Rule\Name::fromString('vendor/composer/version-constraint/replace-wildcard-with-tilde');
    }

    public function target(): Rule\Target
    {
        return Constraints::target();
    }

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Replaces version ranges with a wildcard with version ranges with a tilde in version constraints.',
            Rule\Example::create(
                <<<'JSON'
{
    "require": {
        "ergebnis/json-normalizer": "4.1.*"
    }
}

JSON,
                <<<'JSON'
{
    "require": {
        "ergebnis/json-normalizer": "~4.1.0"
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

        $normalized = Constraints::applyToVersionsInTurn(
            $versionConstraint,
            '{^(\d+(?:\.\d+)*)\.[*xX]$}',
            '~$1.0',
        );

        if ($normalized === $versionConstraint) {
            return Rule\Action::keep();
        }

        return Rule\Action::replace(Parser\Node\StringNode::fromString($normalized));
    }
}
