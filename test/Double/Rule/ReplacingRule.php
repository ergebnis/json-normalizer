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

final class ReplacingRule implements Rule
{
    private Rule\Name $name;
    private Rule\Target $target;
    private Parser\Node\Node $replacement;

    private function __construct(
        Rule\Name $name,
        Rule\Target $target,
        Parser\Node\Node $replacement
    ) {
        $this->name = $name;
        $this->target = $target;
        $this->replacement = $replacement;
    }

    public static function create(
        Rule\Name $name,
        Rule\Target $target,
        Parser\Node\Node $replacement
    ): self {
        return new self(
            $name,
            $target,
            $replacement,
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

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Test double.',
            Rule\Example::create(
                '{}',
                '{}',
            ),
        );
    }

    public function apply(
        Parser\Node\Node $node,
        Context $context
    ): Rule\Action {
        $printer = new Parser\Printer();

        $format = Parser\Format::compact();

        if ($printer->print($node, $format) === $printer->print($this->replacement, $format)) {
            return Rule\Action::keep();
        }

        return Rule\Action::replace($this->replacement);
    }
}
