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

interface FormatterInterface
{
    /**
     * @param string          $json
     * @param FormatInterface $format
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function format(string $json, FormatInterface $format): string;
}
