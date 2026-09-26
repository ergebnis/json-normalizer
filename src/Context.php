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
    private ?Schema $schema;

    private function __construct(
        Parser\Traverser\Path $path,
        ?Schema $schema
    ) {
        $this->path = $path;
        $this->schema = $schema;
    }

    public static function create(
        Parser\Traverser\Path $path,
        ?Schema $schema
    ): self {
        return new self(
            $path,
            $schema,
        );
    }

    public function path(): Parser\Traverser\Path
    {
        return $this->path;
    }

    public function schema(): ?Schema
    {
        return $this->schema;
    }
}
