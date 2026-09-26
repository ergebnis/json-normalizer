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

namespace Ergebnis\Json\Normalizer\Test\Double\Rule;

use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;

final class KeepingRule implements Rule
{
    private Rule\Name $name;
    private Rule\Target $target;

    private function __construct(
        Rule\Name $name,
        Rule\Target $target
    ) {
        $this->name = $name;
        $this->target = $target;
    }

    public static function create(
        Rule\Name $name,
        Rule\Target $target
    ): self {
        return new self(
            $name,
            $target,
        );
    }

    public function name(): Rule\Name
    {
        return $this->name;
    }

    public function target(): Rule\Target
    {
        return $this->target;
    }

    public function apply(
        Parser\Node\Node $node,
        Context $context
    ): Rule\Action {
        return Rule\Action::keep();
    }
}
