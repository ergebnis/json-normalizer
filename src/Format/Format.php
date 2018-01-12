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

final class Format implements FormatInterface
{
    /**
     * @var int
     */
    private $jsonEncodeOptions;

    /**
     * @var string
     */
    private $indent;

    /**
     * @var bool
     */
    private $hasFinalNewLine;

    /**
     * @param int    $jsonEncodeOptions
     * @param string $indent
     * @param bool   $hasFinalNewLine
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(int $jsonEncodeOptions, string $indent, bool $hasFinalNewLine)
    {
        if (0 > $jsonEncodeOptions) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid options for json_encode().',
                $indent
            ));
        }

        if (1 !== \preg_match('/^[ \t]+$/', $indent)) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not a valid indent.',
                $indent
            ));
        }

        $this->jsonEncodeOptions = $jsonEncodeOptions;
        $this->indent = $indent;
        $this->hasFinalNewLine = $hasFinalNewLine;
    }

    public function jsonEncodeOptions(): int
    {
        return $this->jsonEncodeOptions;
    }

    public function indent(): string
    {
        return $this->indent;
    }

    public function hasFinalNewLine(): bool
    {
        return $this->hasFinalNewLine;
    }
}
