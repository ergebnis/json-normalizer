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
     * @var IndentInterface
     */
    private $indent;

    /**
     * @var NewLineInterface
     */
    private $newLine;

    /**
     * @var bool
     */
    private $hasFinalNewLine;

    /**
     * @param int              $jsonEncodeOptions
     * @param IndentInterface  $indent
     * @param NewLineInterface $newLine
     * @param bool             $hasFinalNewLine
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(int $jsonEncodeOptions, IndentInterface $indent, NewLineInterface $newLine, bool $hasFinalNewLine)
    {
        if (0 > $jsonEncodeOptions) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid options for json_encode().',
                $jsonEncodeOptions
            ));
        }

        $this->jsonEncodeOptions = $jsonEncodeOptions;
        $this->indent = $indent;
        $this->newLine = $newLine;
        $this->hasFinalNewLine = $hasFinalNewLine;
    }

    public function jsonEncodeOptions(): int
    {
        return $this->jsonEncodeOptions;
    }

    public function indent(): IndentInterface
    {
        return $this->indent;
    }

    public function newLine(): NewLineInterface
    {
        return $this->newLine;
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

    public function withIndent(IndentInterface $indent): FormatInterface
    {
        $mutated = clone $this;

        $mutated->indent = $indent;

        return $mutated;
    }

    public function withNewLine(NewLineInterface $newLine): FormatInterface
    {
        $mutated = clone $this;

        $mutated->newLine = $newLine;

        return $mutated;
    }

    public function withHasFinalNewLine(bool $hasFinalNewLine): FormatInterface
    {
        $mutated = clone $this;

        $mutated->hasFinalNewLine = $hasFinalNewLine;

        return $mutated;
    }
}
