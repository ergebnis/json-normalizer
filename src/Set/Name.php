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

namespace Ergebnis\Json\Normalizer\Set;

use Ergebnis\Json\Normalizer\Exception;

/**
 * @psalm-immutable
 */
final class Name
{
    private const PATTERN = '/^@[a-z][a-z0-9]*(-[a-z0-9]+)*(\/[a-z][a-z0-9]*(-[a-z0-9]+)*)*$/';
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @throws Exception\InvalidSetName
     */
    public static function fromString(string $value): self
    {
        if (1 !== \preg_match(self::PATTERN, $value)) {
            throw Exception\InvalidSetName::notAtSignFollowedByKebabCaseSegments($value);
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
