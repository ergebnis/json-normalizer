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

final class Indent implements IndentInterface
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
     * @throws Exception\InvalidIndentStringException
     *
     * @return IndentInterface
     */
    public static function fromString(string $string): IndentInterface
    {
        if (1 !== \preg_match('/^( *|\t+)$/', $string)) {
            throw Exception\InvalidIndentStringException::fromString($string);
        }

        return new self($string);
    }

    /**
     * @param int    $size
     * @param string $style
     *
     * @throws Exception\InvalidIndentSizeException
     * @throws Exception\InvalidIndentStyleException
     *
     * @return IndentInterface
     */
    public static function fromSizeAndStyle(int $size, string $style): IndentInterface
    {
        $minimumSize = 1;

        if ($minimumSize > $size) {
            throw Exception\InvalidIndentSizeException::fromSizeAndMinimumSize(
                $size,
                $minimumSize
            );
        }

        $characters = [
            'space' => ' ',
            'tab' => "\t",
        ];

        if (!\array_key_exists($style, $characters)) {
            throw Exception\InvalidIndentStyleException::fromStyleAndAllowedStyles(
                $style,
                ...\array_keys($characters)
            );
        }

        $value = \str_repeat(
            $characters[$style],
            $size
        );

        return new self($value);
    }
}
