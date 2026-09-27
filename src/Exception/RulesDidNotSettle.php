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

use Ergebnis\Json\Normalizer\Change;

final class RulesDidNotSettle extends \RuntimeException implements Exception
{
    private int $passes = 0;

    /**
     * @var list<Change>
     */
    private array $changes = [];

    public static function after(
        int $passes,
        Change ...$changes
    ): self {
        $descriptions = \array_map(static function (Change $change): string {
            return \sprintf(
                '"%s" at "%s"',
                $change->rule()->toString(),
                $change->path()->toJsonPointer()->toJsonString(),
            );
        }, $changes);

        $exception = new self(\sprintf(
            'Rules did not settle after %d passes; the last pass changed %s.',
            $passes,
            self::join($descriptions),
        ));

        $exception->passes = $passes;
        $exception->changes = $changes;

        return $exception;
    }

    public function passes(): int
    {
        return $this->passes;
    }

    /**
     * @return list<Change>
     */
    public function changes(): array
    {
        return $this->changes;
    }

    /**
     * @param array<int, string> $descriptions
     */
    private static function join(array $descriptions): string
    {
        $last = \array_pop($descriptions);

        if ([] === $descriptions) {
            return (string) $last;
        }

        return \sprintf(
            '%s and %s',
            \implode(
                ', ',
                $descriptions,
            ),
            $last,
        );
    }
}
