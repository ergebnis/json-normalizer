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

use Composer\Semver;
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

    public function __construct(private Semver\VersionParser $versionParser)
    {
    }

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

            $decoded->{$name} = \array_map(function (string $versionConstraint): string {
                return $this->normalizeVersionConstraint($versionConstraint);
            }, $packages);
        }

        /** @var string $encoded */
        $encoded = \json_encode(
            $decoded,
            Format\JsonEncodeOptions::default()->toInt(),
        );

        return Json::fromString($encoded);
    }

    private function normalizeVersionConstraint(string $versionConstraint): string
    {
        $normalized = self::trimVersionConstraint($versionConstraint);

        try {
            $this->versionParser->parseConstraints($normalized);
        } catch (\UnexpectedValueException) {
            return $normalized;
        }

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
                if (\str_contains($range, ' as ')) {
                    $andGroups = [];
                    $temp = \explode(' ', $range);

                    while ([] !== $temp) {
                        if ('as' === $temp[0]) {
                            \array_shift($temp);
                            $andGroups[\count($andGroups) - 1] .= ' as ' . \array_shift($temp);
                        } else {
                            $andGroups[] = \array_shift($temp);
                        }
                    }
                } else {
                    $andGroups = \explode(' ', $range);
                }

                \usort($andGroups, $sorter);
                $range = \implode(' ', $andGroups);
            }

            \usort($ranges, $sorter);
            $or = \implode(' - ', $ranges);
        }

        \usort($orGroups, $sorter);

        return \implode(' || ', $orGroups);
    }

    private static function trimVersionConstraint(string $versionConstraint): string
    {
        return \trim(\str_replace(
            '  ',
            ' ',
            $versionConstraint,
        ));
    }
}
