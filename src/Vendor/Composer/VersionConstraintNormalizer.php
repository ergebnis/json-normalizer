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
        $normalized = self::trimOuter($versionConstraint);

        try {
            $this->versionParser->parseConstraints($normalized);
        } catch (\UnexpectedValueException) {
            return $normalized;
        }

        $normalized = self::normalizeAnd($normalized);
        $normalized = self::normalizeOr($normalized);
        $normalized = self::trimInner($normalized);
        $normalized = self::replaceWildcardWithTilde($normalized);
        $normalized = self::replaceTildeWithCaret($normalized);
        $normalized = self::assertCorrectNumberOfParts($normalized);

        return self::sortOrGroups($normalized);
    }

    private static function trimOuter(string $versionConstraint): string
    {
        return \trim(\str_replace(
            '  ',
            ' ',
            $versionConstraint,
        ));
    }

    private static function normalizeAnd(string $versionConstraint): string
    {
        /** @var array<int, string> $versionConstraints */
        $versionConstraints = \preg_split(
            '/\s*,\s*/',
            $versionConstraint,
        );

        return \implode(
            ' ',
            $versionConstraints,
        );
    }

    private static function replaceWildcardWithTilde(string $versionConstraint): string {
        $split = \explode(' ', $versionConstraint);

        foreach ($split as &$part) {
            $part = \preg_replace('{^(\d+(?:\.\d+)*)\.\*$}', '~$1.0', $part);
        }

        return \implode(' ', $split);
    }

    private static function replaceTildeWithCaret(string $versionConstraint): string {
        $split = \explode(' ', $versionConstraint);

        foreach ($split as &$part) {
            $part = \preg_replace('{^~(\d+(?:\.\d+)?)$}', '^$1', $part);
        }

        return \implode(' ', $split);
    }

    private static function assertCorrectNumberOfParts(string $versionConstraint): string {
        $split = \explode(' ', $versionConstraint);

        foreach ($split as &$part) {
            // Assert minimum number of version number parts for the caret operator
            $part = \preg_replace('{^(\^\d+)$}', '$1.0', $part);

            // Trim extra version number parts for caret operator
            $part = \preg_replace('{^(\^[1-9]\d*\.\d+)\.0$}', '$1', $part);
        }

        return \implode(' ', $split);
    }

    private static function normalizeOr(string $versionConstraint): string
    {
        /** @var array<int, string> $versionConstraints */
        $versionConstraints = \preg_split(
            '/\s*\|\|?\s*/',
            $versionConstraint,
        );

        return \implode(
            ' || ',
            $versionConstraints,
        );
    }

    private static function trimInner(string $versionConstraint): string
    {
        return \preg_replace(
            '/\s+/',
            ' ',
            $versionConstraint,
        );
    }

    private static function sortOrGroups(string $versionConstraint): string
    {
        $normalize = static function (string $versionConstraint): string {
            return \trim($versionConstraint, '<>=!~^');
        };

        $sort = static function (string $a, string $b) use ($normalize): int {
            return \strcmp(
                $normalize($a),
                $normalize($b),
            );
        };

        $orGroups = \explode(' || ', $versionConstraint);

        $orGroups = \array_map(static function (string $or) use ($sort): string {
            $ranges = \explode(' - ', $or);

            $ranges = \array_map(static function (string $range) use ($sort): string {
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

                \usort($andGroups, $sort);

                return \implode(' ', $andGroups);
            }, $ranges);

            \usort($ranges, $sort);

            return \implode(' - ', $ranges);
        }, $orGroups);

        \usort($orGroups, $sort);

        do {
            $hasChanged = false;

            for ($i = 0, $iMax = \count($orGroups) - 1; $i < $iMax; ++$i) {
                $a = $orGroups[$i];
                $b = $orGroups[$i + 1];

                $regex = '{^[~^]\d+(?:\.\d+)*$}';

                if (1 === \preg_match($regex, $a) && 1 === \preg_match($regex, $b)) {
                    if (Semver\Semver::satisfies(\ltrim($b, '^~'), $a)) {
                        // Remove overlapping constraints
                        $hasChanged = true;
                        $orGroups[$i + 1] = null;
                        $orGroups = \array_values(\array_filter($orGroups));

                        break;
                    }
                }
            }
        } while ($hasChanged);

        return \implode(' || ', $orGroups);
    }
}
