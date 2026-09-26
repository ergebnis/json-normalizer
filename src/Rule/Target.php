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

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;

/**
 * @psalm-immutable
 */
final class Target
{
    private Pointer\Specification $location;

    /**
     * @var list<class-string<Parser\Node\Node>>
     */
    private array $nodeClasses;

    /**
     * @param list<class-string<Parser\Node\Node>> $nodeClasses
     */
    private function __construct(
        Pointer\Specification $location,
        array $nodeClasses
    ) {
        $this->location = $location;
        $this->nodeClasses = $nodeClasses;
    }

    /**
     * @throws Exception\InvalidTarget
     */
    public static function create(
        Pointer\Specification $location,
        string ...$nodeClasses
    ): self {
        if ([] === $nodeClasses) {
            throw Exception\InvalidTarget::withoutNodeClasses();
        }

        $validated = [];

        foreach ($nodeClasses as $nodeClass) {
            if (!\is_a($nodeClass, Parser\Node\Node::class, true)) {
                throw Exception\InvalidTarget::notNodeClass($nodeClass);
            }

            $validated[] = $nodeClass;
        }

        return new self(
            $location,
            $validated,
        );
    }

    public function location(): Pointer\Specification
    {
        return $this->location;
    }

    /**
     * @return list<class-string<Parser\Node\Node>>
     */
    public function nodeClasses(): array
    {
        return $this->nodeClasses;
    }

    public function matches(
        Parser\Node\Node $node,
        Parser\Traverser\Path $path
    ): bool {
        foreach ($this->nodeClasses as $nodeClass) {
            if (!$node instanceof $nodeClass) {
                continue;
            }

            return $this->location->isSatisfiedBy($path->toJsonPointer());
        }

        return false;
    }
}
