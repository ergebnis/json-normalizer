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
     * Constant for a regular expression matching valid indents.
     */
    private const PATTERN_INDENT = '/^[ \t]+$/';

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

        if (1 !== \preg_match(self::PATTERN_INDENT, $indent)) {
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

    public function withJsonEncodeOptions(int $jsonEncodeOptions): FormatInterface
    {
        if (0 > $jsonEncodeOptions) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid options for json_encode().',
                $jsonEncodeOptions
            ));
        }

        $mutated = clone $this;

        $mutated->jsonEncodeOptions = $jsonEncodeOptions;

        return $mutated;
    }

    public function withIndent(string $indent): FormatInterface
    {
        if (1 !== \preg_match(self::PATTERN_INDENT, $indent)) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not a valid indent.',
                $indent
            ));
        }

        $mutated = clone $this;

        $mutated->indent = $indent;

        return $mutated;
    }

    public function withHasFinalNewLine(bool $hasFinalNewLine): FormatInterface
    {
        $mutated = clone $this;

        $mutated->hasFinalNewLine = $hasFinalNewLine;

        return $mutated;
    }
}
