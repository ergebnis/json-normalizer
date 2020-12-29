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
     * @phpstan-var list<string>
     * @psalm-var list<string>
     *
     * @var array<int, string>
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
            $decoded->{$name} = self::sortByKey($value);
        }

        /** @var string $encoded */
        $encoded = \json_encode($decoded);

        return Json::fromEncoded($encoded);
    }

    /**
     * @param null|array|bool|false|\stdClass|string $value
     *
     * @return null|array|bool|false|\stdClass|string
     */
    private static function sortByKey($value)
    {
        if (!\is_object($value)) {
            return $value;
        }

        $sorted = (array) $value;

        if ([] === $sorted) {
            return $value;
        }

        \ksort($sorted);

        return \array_map(static function ($value) {
            return self::sortByKey($value);
        }, $sorted);
    }
}
