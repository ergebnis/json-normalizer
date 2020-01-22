<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer;

use Ergebnis\Json\Normalizer\Format\NewLine;

final class FinalNewLineNormalizer implements NormalizerInterface
{
    public function normalize(Json $json): Json
    {
        $newLine = NewLine::fromJson($json);
        $withFinalNewLine = \rtrim($json->encoded()) . $newLine->__toString();

        return Json::fromEncoded($withFinalNewLine);
    }
}
