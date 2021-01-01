<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Vendor\Composer;

use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\NormalizerInterface;

final class VersionConstraintNormalizer implements NormalizerInterface
{
    private const PROPERTIES_THAT_SHOULD_BE_NORMALIZED = [
        'conflict',
        'provide',
        'replace',
        'require',
        'require-dev',
    ];

    private const MAP = [
        'and' => [
            '{\s*,\s*}',
            ',',
        ],
        'or' => [
            '{\s*\|\|?\s*}',
            ' || ',
        ],
        'range' => [
            '{\s+}',
            ' ',
        ],
    ];

    public function normalize(Json $json): Json
    {
        $decoded = $json->decoded();

        if (!\is_object($decoded)) {
            return $json;
        }

        $objectPropertiesThatShouldBeNormalized = \array_intersect_key(
            \get_object_vars($decoded),
            \array_flip(self::PROPERTIES_THAT_SHOULD_BE_NORMALIZED)
        );

        if (0 === \count($objectPropertiesThatShouldBeNormalized)) {
            return $json;
        }

        foreach ($objectPropertiesThatShouldBeNormalized as $name => $value) {
            $packages = (array) $decoded->{$name};

            if (0 === \count($packages)) {
                continue;
            }

            $decoded->{$name} = \array_map(function (string $versionConstraint): string {
                return self::normalizeVersionConstraint($versionConstraint);
            }, $packages);
        }

        /** @var string $encoded */
        $encoded = \json_encode($decoded);

        return Json::fromEncoded($encoded);
    }

    private static function normalizeVersionConstraint(string $versionConstraint): string
    {
        $normalized = $versionConstraint;

        foreach (self::MAP as [$pattern, $glue]) {
            /** @var array<int, string> $split */
            $split = \preg_split(
                $pattern,
                $normalized
            );

            $normalized = \implode(
                $glue,
                $split
            );
        }

        return \trim($normalized);
    }
}
