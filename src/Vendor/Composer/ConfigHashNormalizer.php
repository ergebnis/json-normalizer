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

final class ConfigHashNormalizer implements NormalizerInterface
{
    private const PROPERTIES_THAT_SHOULD_BE_SORTED = [
        'config',
        'extra',
        'scripts-descriptions',
    ];

    /**
     * @see https://getcomposer.org/doc/06-config.md#allow-plugins
     * @see https://getcomposer.org/doc/06-config.md#preferred-install
     */
    private const PROPERTY_PATHS_THAT_SHOULD_NOT_BE_SORTED = [
        'config.allow-plugins',
        'config.preferred-install',
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

        if (0 === \count($objectPropertiesThatShouldBeNormalized)) {
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
     * @param null|array|bool|false|\stdClass|string $value
     *
     * @return null|array|bool|false|\stdClass|string
     */
    private static function sortByKey(
        string $propertyPath,
        $value
    ) {
        if (\in_array($propertyPath, self::PROPERTY_PATHS_THAT_SHOULD_NOT_BE_SORTED, true)) {
            return $value;
        }

        if (!\is_object($value)) {
            return $value;
        }

        /** @var array<string, mixed> $sorted */
        $sorted = (array) $value;

        if ([] === $sorted) {
            return $value;
        }

        \ksort($sorted);

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
