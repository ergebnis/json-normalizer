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

use Localheinz\Json\Json;
use Localheinz\Json\JsonInterface;

final class NoFinalNewLineNormalizer implements NormalizerInterface
{
    public function normalize(JsonInterface $json): JsonInterface
    {
        $withFinalNewLine = \rtrim($json->encoded());

        return Json::fromEncoded($withFinalNewLine);
    }
}
