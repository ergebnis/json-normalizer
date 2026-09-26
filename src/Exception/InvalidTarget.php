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

namespace Ergebnis\Json\Normalizer\Exception;

use Ergebnis\Json\Parser;

final class InvalidTarget extends \InvalidArgumentException implements Exception
{
    private string $value = '';

    public static function withoutNodeClasses(): self
    {
        return new self('Target must name at least one node class.');
    }

    public static function notNodeClass(string $value): self
    {
        $exception = new self(\sprintf(
            'Target node class "%s" must implement "%s".',
            $value,
            Parser\Node\Node::class,
        ));

        $exception->value = $value;

        return $exception;
    }

    public function value(): string
    {
        return $this->value;
    }
}
