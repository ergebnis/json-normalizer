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

namespace Ergebnis\Json\Normalizer\Rule;

use Ergebnis\Json\Parser;

/**
 * @psalm-immutable
 */
final class Action
{
    private bool $remove;
    private ?Parser\Node\Node $replacement;

    private function __construct(
        bool $remove,
        ?Parser\Node\Node $replacement
    ) {
        $this->remove = $remove;
        $this->replacement = $replacement;
    }

    public static function keep(): self
    {
        return new self(
            false,
            null,
        );
    }

    public static function replace(Parser\Node\Node $node): self
    {
        return new self(
            false,
            $node,
        );
    }

    public static function remove(): self
    {
        return new self(
            true,
            null,
        );
    }

    public function isKeep(): bool
    {
        if ($this->remove) {
            return false;
        }

        return !$this->replacement instanceof Parser\Node\Node;
    }

    public function isRemove(): bool
    {
        return $this->remove;
    }

    public function replacement(): ?Parser\Node\Node
    {
        return $this->replacement;
    }
}
