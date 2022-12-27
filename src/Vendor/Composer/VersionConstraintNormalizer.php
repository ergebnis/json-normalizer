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

use Composer\Semver\Semver;
use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\Normalizer;

final class VersionConstraintNormalizer implements Normalizer
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
            ' ',
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
            \array_flip(self::PROPERTIES_THAT_SHOULD_BE_NORMALIZED),
        );

        if ([] === $objectPropertiesThatShouldBeNormalized) {
            return $json;
        }

        foreach ($objectPropertiesThatShouldBeNormalized as $name => $value) {
            $packages = (array) $decoded->{$name};

            if ([] === $packages) {
                continue;
            }

            $decoded->{$name} = \array_map(static function (string $versionConstraint): string {
                return self::normalizeVersionConstraint($versionConstraint);
            }, $packages);
        }

        /** @var string $encoded */
        $encoded = \json_encode(
            $decoded,
            Format\JsonEncodeOptions::default()->toInt(),
        );

        return Json::fromString($encoded);
    }

    private static function normalizeVersionConstraint(string $versionConstraint): string
    {
        $normalized = \trim($versionConstraint);

        foreach (self::MAP as [$pattern, $glue]) {
            /** @var array<int, string> $split */
            $split = \preg_split(
                $pattern,
                $normalized,
            );

            $normalized = \implode(
                $glue,
                $split,
            );
        }

        $split = \explode(' ', $normalized);

        foreach ($split as &$part) {
            // Replace wildcard version range with tilde operator
            $part = \preg_replace('{^(\d+(?:\.\d+)*)\.\*$}', '~$1.0', $part);

            // Prefer caret operator when equivalent tilde operator was used
            $part = \preg_replace('{^~(\d+(?:\.\d+)?)$}', '^$1', $part);

            // Assert minimum number of version number parts for the caret operator
            $part = \preg_replace('{^(\^\d+)$}', '$1.0', $part);
        }

        $normalized = \implode(' ', $split);

        // Sort
        $sorter = static function (string $a, string $b): int {
            $a = \trim($a, '<>=!~^');
            $b = \trim($b, '<>=!~^');

            return \strcmp($a, $b);
        };

        $orGroups = \explode(' || ', $normalized);

        foreach ($orGroups as &$or) {
            $ranges = \explode(' - ', $or);

            foreach ($ranges as &$range) {
                $andGroups = \explode(' ', $range);
                \usort($andGroups, $sorter);
                $range = \implode(' ', $andGroups);
            }

            \usort($ranges, $sorter);
            $or = \implode(' - ', $ranges);
        }

        \usort($orGroups, $sorter);

        do {
            $hasChanged = false;

            for ($i = 0, $iMax = \count($orGroups) - 1; $i < $iMax; ++$i) {
                $a = $orGroups[$i];
                $b = $orGroups[$i + 1];

                $regex = '{^[~^]\d+(?:\.\d+)*$}';

                if (\preg_match($regex, $a) && \preg_match($regex, $b)) {
                    if (Semver::satisfies(\ltrim($b, '^~'), $a)) {
                        // Remove overlapping constraints
                        $hasChanged = true;
                        $orGroups[$i + 1] = null;
                        $orGroups = \array_values(\array_filter($orGroups));

                        break;
                    }
                }
            }
        } while ($hasChanged);

        $normalized = \implode(' || ', $orGroups);

        return $normalized;
    }
}
