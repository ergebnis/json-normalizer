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

namespace Ergebnis\Json\Normalizer\Vendor\Composer;

use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\NormalizerInterface;

final class ConfigHashNormalizer implements NormalizerInterface
{
    /**
     * @var string[]
     */
    private static $properties = [
        'config',
        'extra',
        'scripts-descriptions',
    ];

    public function normalize(Json $json): Json
    {
        $decoded = $json->decoded();

        if (!\is_object($decoded)) {
            return $json;
        }

        $objectProperties = \array_intersect_key(
            \get_object_vars($decoded),
            \array_flip(self::$properties)
        );

        if (0 === \count($objectProperties)) {
            return $json;
        }

        foreach ($objectProperties as $name => $value) {
            $config = (array) $decoded->{$name};

            if (0 === \count($config)) {
                return $json;
            }

            \ksort($config);

            $decoded->{$name} = $config;
        }

        /** @var string $encoded */
        $encoded = \json_encode($decoded);

        return Json::fromEncoded($encoded);
    }
}
