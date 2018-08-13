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

final class Indent implements IndentInterface
{
    /**
     * @var string
     */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param string $string
     *
     * @throws \InvalidArgumentException
     *
     * @return IndentInterface
     */
    public static function fromString(string $string): IndentInterface
    {
        if (1 !== \preg_match('/^( *|\t+)$/', $string)) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not a valid indent.',
                $string
            ));
        }

        return new self($string);
    }

    /**
     * @param int    $size
     * @param string $style
     *
     * @throws \InvalidArgumentException
     *
     * @return IndentInterface
     */
    public static function fromSizeAndStyle(int $size, string $style): IndentInterface
    {
        if (1 > $size) {
            throw new \InvalidArgumentException(\sprintf(
                'Size needs to be greater than 0, but %d is not.',
                $size
            ));
        }

        $characters = [
            'space' => ' ',
            'tab' => "\t",
        ];

        if (!\array_key_exists($style, $characters)) {
            throw new \InvalidArgumentException(\sprintf(
                'Style needs to be one of "%s", but "%s" is not.',
                \implode('", "', \array_keys($characters)),
                $style
            ));
        }

        $value = \str_repeat(
            $characters[$style],
            $size
        );

        return new self($value);
    }
}
