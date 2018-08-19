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

final class NewLine implements NewLineInterface
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
     * @return NewLineInterface
     */
    public static function fromString(string $string): NewLineInterface
    {
        if (1 !== \preg_match('/^(?>\r\n|\n|\r)$/', $string)) {
            throw Exception\InvalidNewLineStringException::fromString($string);
        }

        return new self($string);
    }
}
