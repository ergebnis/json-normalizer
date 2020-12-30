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
    private static $propertiesThatShouldBeSorted = [
        'config',
        'extra',
        'scripts-descriptions',
    ];

    /**
     * @phpstan-var list<string>
     * @psalm-var list<string>
     *
     * @var array<int, string>
     */
    private static $propertiesThatShouldNotBeSorted = [
        'preferred-install',
    ];

    public function normalize(Json $json): Json
    {
        $decoded = $json->decoded();

        if (!\is_object($decoded)) {
            return $json;
        }

        $objectPropertiesThatShouldBeNormalized = \array_intersect_key(
            \get_object_vars($decoded),
            \array_flip(self::$propertiesThatShouldBeSorted)
        );

        if (0 === \count($objectPropertiesThatShouldBeNormalized)) {
            return $json;
        }

        foreach ($objectPropertiesThatShouldBeNormalized as $name => $value) {
            $decoded->{$name} = self::sortByKey(
                $name,
                $value
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
    private static function sortByKey(string $name, $value)
    {
        if (\in_array($name, self::$propertiesThatShouldNotBeSorted, true)) {
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
            \array_map(static function ($value, string $name) {
                return self::sortByKey(
                    $name,
                    $value
                );
            }, $sorted, $names)
        );
    }
}
