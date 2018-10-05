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

namespace Localheinz\Json\Normalizer;

interface JsonInterface
{
    /**
     * Returns the original JSON value.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Returns the original JSON value.
     *
     * @return string
     */
    public function encoded(): string;

    /**
     * Returns the decoded JSON value.
     *
     * @return null|array|bool|float|int|\stdClass|string
     */
    public function decoded();

    /**
     * Returns the format of the original JSON value.
     *
     * @return Format\Format
     */
    public function format(): Format\Format;
}
