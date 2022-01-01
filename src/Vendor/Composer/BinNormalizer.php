<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Vendor\Composer;

use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\NormalizerInterface;

final class BinNormalizer implements NormalizerInterface
{
    public function normalize(Json $json): Json
    {
        $decoded = $json->decoded();

        if (
            !\is_object($decoded)
            || !\property_exists($decoded, 'bin')
            || !\is_array($decoded->bin)
        ) {
            return $json;
        }

        $bin = $decoded->bin;

        \sort($bin);

        $decoded->bin = $bin;

        /** @var string $encoded */
        $encoded = \json_encode($decoded);

        return Json::fromEncoded($encoded);
    }
}
