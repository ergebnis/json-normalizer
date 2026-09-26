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
 * @psalm-immutable
 */
final class Change
{
    private Rule\Name $rule;
    private Parser\Traverser\Path $path;

    private function __construct(
        Rule\Name $rule,
        Parser\Traverser\Path $path
    ) {
        $this->rule = $rule;
        $this->path = $path;
    }

    public static function create(
        Rule\Name $rule,
        Parser\Traverser\Path $path
    ): self {
        return new self(
            $rule,
            $path,
        );
    }

    public function rule(): Rule\Name
    {
        return $this->rule;
    }

    public function path(): Parser\Traverser\Path
    {
        return $this->path;
    }
}
