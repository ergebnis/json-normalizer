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

interface FormatInterface
{
    public function jsonEncodeOptions(): int;

    public function indent(): string;

    public function hasFinalNewLine(): bool;

    /**
     * @param string $indent
     *
     * @throws \InvalidArgumentException
     *
     * @return FormatInterface
     */
    public function withIndent(string $indent): self;

    /**
     * @param int $jsonEncodeOptions
     *
     * @throws \InvalidArgumentException
     *
     * @return FormatInterface
     */
    public function withJsonEncodeOptions(int $jsonEncodeOptions): self;

    public function withHasFinalNewLine(bool $hasFinalNewLine): self;
}
