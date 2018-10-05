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

interface FormatInterface
{
    public function jsonEncodeOptions(): int;

    public function indent(): Indent;

    public function newLine(): NewLineInterface;

    public function hasFinalNewLine(): bool;

    /**
     * @param int $jsonEncodeOptions
     *
     * @throws Exception\InvalidJsonEncodeOptionsException
     *
     * @return FormatInterface
     */
    public function withJsonEncodeOptions(int $jsonEncodeOptions): self;

    public function withIndent(Indent $indent): self;

    public function withNewLine(NewLineInterface $newLine): self;

    public function withHasFinalNewLine(bool $hasFinalNewLine): self;
}
