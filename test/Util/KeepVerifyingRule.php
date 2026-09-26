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

namespace Ergebnis\Json\Normalizer\Test\Util;

use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;

final class KeepVerifyingRule implements Rule
{
    private Rule $rule;
    private Parser\Printer $printer;

    private function __construct(Rule $rule)
    {
        $this->rule = $rule;
        $this->printer = new Parser\Printer();
    }

    public static function create(Rule $rule): self
    {
        return new self($rule);
    }

    public function name(): Rule\Name
    {
        return $this->rule->name();
    }

    public function target(): Rule\Target
    {
        return $this->rule->target();
    }

    public function apply(
        Parser\Node\Node $node,
        Context $context
    ): Rule\Action {
        $before = $this->printer->print(
            $node,
            Parser\Format::compact(),
        );

        $action = $this->rule->apply(
            $node,
            $context,
        );

        if (!$action->isKeep()) {
            return $action;
        }

        $after = $this->printer->print(
            $node,
            Parser\Format::compact(),
        );

        if ($before !== $after) {
            throw new \LogicException(\sprintf(
                'Rule "%s" returned keep() at "%s" but changed the node from %s to %s.',
                $this->rule->name()->toString(),
                $context->path()->toJsonPointer()->toJsonString(),
                $before,
                $after,
            ));
        }

        return $action;
    }
}
