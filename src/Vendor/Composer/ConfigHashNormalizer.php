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
use Ergebnis\Json\Normalizer\Normalizer;

final class ConfigHashNormalizer implements Normalizer
{
    private const PROPERTIES_THAT_SHOULD_BE_SORTED = [
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

        $objectPropertiesThatShouldBeNormalized = \array_intersect_key(
            \get_object_vars($decoded),
            \array_flip(self::PROPERTIES_THAT_SHOULD_BE_SORTED),
        );

        if ([] === $objectPropertiesThatShouldBeNormalized) {
            return $json;
        }

        foreach ($objectPropertiesThatShouldBeNormalized as $name => $value) {
            $decoded->{$name} = self::sortByKey(
                $name,
                $value,
            );
        }

        /** @var string $encoded */
        $encoded = \json_encode($decoded);

        return Json::fromEncoded($encoded);
    }

    /**
     * @param null|array|bool|false|object|string $value
     *
     * @return null|array|bool|false|object|string
     */
    private static function sortByKey(
        string $propertyPath,
        $value
    ) {
        if (!\is_object($value)) {
            return $value;
        }

        /** @var array<string, mixed> $sorted */
        $sorted = (array) $value;

        if ([] === $sorted) {
            return $value;
        }

        // @see https://getcomposer.org/doc/06-config.md#allow-plugins
        // @see https://getcomposer.org/doc/06-config.md#preferred-install
        \uksort($sorted, static function (string $a, string $b) {
            // '*' = ASCII 42 (ie, before all letters, numbers, and dash)
            // '~' = ASCII 126 (ie, after all letters, numbers, and dash)
            $a = \str_replace('*', '~', $a);
            $b = \str_replace('*', '~', $b);

            return \strcmp($a, $b);
        });

        $names = \array_keys($sorted);

        return \array_combine(
            $names,
            \array_map(static function ($value, string $name) use ($propertyPath) {
                return self::sortByKey(
                    \sprintf(
                        '%s.%s',
                        $propertyPath,
                        $name,
                    ),
                    $value,
                );
            }, $sorted, $names),
        );
    }
}
