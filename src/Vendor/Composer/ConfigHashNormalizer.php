<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Vendor\Composer;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\Normalizer;

final class ConfigHashNormalizer implements Normalizer
{
    private const PROPERTIES_WITH_WILDCARDS = [
        /**
         * @see https://getcomposer.org/doc/06-config.md#allow-plugins
         */
        'allow-plugins',
        /**
         * @see https://getcomposer.org/doc/06-config.md#preferred-install
         */
        'preferred-install',
    ];

    public function normalize(Json $json): Json
    {
        $decoded = $json->decoded();

        if (!\is_object($decoded)) {
            return $json;
        }

        if (!\property_exists($decoded, 'config')) {
            return $json;
        }

        if (!\is_object($decoded->config)) {
            return $json;
        }

        /** @var array<string, mixed> $config */
        $config = (array) $decoded->config;

        if ([] === $config) {
            return $json;
        }

        \ksort($config);

        foreach (self::PROPERTIES_WITH_WILDCARDS as $property) {
            self::sortPropertyWithWildcard(
                $config,
                $property,
            );
        }

        $decoded->config = $config;

        /** @var string $encoded */
        $encoded = \json_encode(
            $decoded,
            Format\JsonEncodeOptions::default()->toInt(),
        );

        return Json::fromString($encoded);
    }

    /**
     * When sorting with wildcards, special care needs to be taken.
     *
     * @see https://github.com/ergebnis/json-normalizer/pull/775#issuecomment-1346095415
     * @see https://github.com/composer/composer/blob/2.6.5/src/Composer/Plugin/PluginManager.php#L85-L86
     * @see https://github.com/composer/composer/blob/2.6.5/src/Composer/Plugin/PluginManager.php#L626-L646
     * @see https://github.com/composer/composer/blob/2.6.5/src/Composer/Package/BasePackage.php#L252-L257
     * @see https://github.com/composer/composer/blob/2.6.5/src/Composer/Plugin/PluginManager.php#L687-L691
     */
    private static function sortPropertyWithWildcard(
        array &$config,
        string $property,
    ): void {
        if (!\array_key_exists($property, $config)) {
            return;
        }

        if (!\is_object($config[$property])) {
            return;
        }

        $value = (array) $config[$property];

        if ([] === $value) {
            return;
        }

        foreach (\array_keys($value) as $package) {
            /** @var string $package */
            if (\str_contains(\rtrim($package, '*'), '*')) {
                // We cannot reliably sort allow-plugins when there's a wildcard other than at the end of the string.
                return;
            }
        }

        $normalize = static function (string $package): string {
            // Any key with an asterisk needs to be the last entry in its group
            return \str_replace(
                '*',
                '~',
                $package,
            );
        };

        /** @var array<string, mixed> $value */
        \uksort($value, static function (string $a, string $b) use ($normalize): int {
            return \strcmp(
                $normalize($a),
                $normalize($b),
            );
        });

        $config[$property] = $value;
    }
}
