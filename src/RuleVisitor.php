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

use Ergebnis\Json\Parser;

/**
 * @internal
 */
final class RuleVisitor implements Parser\Traverser\Visitor
{
    private Configuration $configuration;

    /**
     * @var list<Rule>
     */
    private array $rules;

    /**
     * @var list<Change>
     */
    private array $changes = [];

    private function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->rules = $configuration->rules();
    }

    public static function create(Configuration $configuration): self
    {
        return new self($configuration);
    }

    public function enter(
        Parser\Node\Node $node,
        Parser\Traverser\Path $path
    ): Parser\Traverser\EnterAction {
        return Parser\Traverser\EnterAction::keep();
    }

    public function leave(
        Parser\Node\Node $node,
        Parser\Traverser\Path $path
    ): Parser\Traverser\LeaveAction {
        $current = $node;
        $replaced = false;

        foreach ($this->rules as $rule) {
            if (!$rule->target()->matches($current, $path)) {
                continue;
            }

            if ($this->configuration->skipFor($rule->name())->isSatisfiedBy($path->toJsonPointer())) {
                continue;
            }

            $action = $rule->apply(
                $current,
                Context::create($path),
            );

            if ($action->isKeep()) {
                continue;
            }

            $this->changes[] = Change::create(
                $rule->name(),
                $path,
            );

            if ($action->isRemove()) {
                return Parser\Traverser\LeaveAction::remove();
            }

            $replacement = $action->replacement();

            if ($replacement instanceof Parser\Node\Node) {
                $current = $replacement;
                $replaced = true;
            }
        }

        if ($replaced) {
            return Parser\Traverser\LeaveAction::replace($current);
        }

        return Parser\Traverser\LeaveAction::keep();
    }

    /**
     * @return list<Change>
     */
    public function changes(): array
    {
        return $this->changes;
    }
}
