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

use Localheinz\Json\Format\FormatInterface;
use Localheinz\Json\JsonInterface;

interface FormatterInterface
{
    public function format(JsonInterface $json, FormatInterface $format): JsonInterface;
}
