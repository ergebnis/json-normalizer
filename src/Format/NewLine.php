<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Format;

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Json;

/**
 * @psalm-immutable
 */
final class NewLine
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @throws Exception\InvalidNewLineStringException
     */
    public static function fromString(string $value): self
    {
        if (1 !== \preg_match('/^(?>\r\n|\n|\r)$/', $value)) {
            throw Exception\InvalidNewLineStringException::fromString($value);
        }

        return new self($value);
    }

    public static function fromJson(Json $json): self
    {
        if (1 === \preg_match('/(?P<newLine>\r\n|\n|\r)/', $json->encoded(), $match)) {
            return self::fromString($match['newLine']);
        }

        return self::fromString(\PHP_EOL);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
