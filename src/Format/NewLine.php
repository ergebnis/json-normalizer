<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Format;

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Json;

final class NewLine
{
    /**
     * @var string
     */
    private $string;

    private function __construct(string $string)
    {
        $this->string = $string;
    }

    public function __toString(): string
    {
        return $this->string;
    }

    /**
     * @throws Exception\InvalidNewLineStringException
     */
    public static function fromString(string $string): self
    {
        if (1 !== \preg_match('/^(?>\r\n|\n|\r)$/', $string)) {
            throw Exception\InvalidNewLineStringException::fromString($string);
        }

        return new self($string);
    }

    public static function fromJson(Json $json): self
    {
        if (1 === \preg_match('/(?P<newLine>\r\n|\n|\r)/', $json->encoded(), $match)) {
            return self::fromString($match['newLine']);
        }

        return self::fromString(\PHP_EOL);
    }
}
