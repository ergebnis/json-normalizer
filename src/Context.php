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
final class Context
{
    private Parser\Traverser\Path $path;

    private function __construct(Parser\Traverser\Path $path)
    {
        $this->path = $path;
    }

    public static function create(Parser\Traverser\Path $path): self
    {
        return new self($path);
    }

    public function path(): Parser\Traverser\Path
    {
        return $this->path;
    }
}
