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

use Ergebnis\Json\Pointer;

/**
 * @psalm-immutable
 */
final class Skip
{
    private Rule\Name $rule;
    private Pointer\Specification $specification;

    private function __construct(
        Rule\Name $rule,
        Pointer\Specification $specification
    ) {
        $this->rule = $rule;
        $this->specification = $specification;
    }

    public static function create(
        Rule\Name $rule,
        Pointer\Specification $specification
    ): self {
        return new self(
            $rule,
            $specification,
        );
    }

    public function rule(): Rule\Name
    {
        return $this->rule;
    }

    public function specification(): Pointer\Specification
    {
        return $this->specification;
    }
}
