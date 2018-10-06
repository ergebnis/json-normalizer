<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Format;

use Localheinz\Json\Normalizer\Exception;
use Localheinz\Json\Normalizer\Json;

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
     * @param string $string
     *
     * @throws Exception\InvalidNewLineStringException
     *
     * @return self
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
