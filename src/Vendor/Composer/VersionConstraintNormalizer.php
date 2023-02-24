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

        $normalized = self::normalizeVersionConstraintSeparators($normalized);
        $normalized = self::trimInner($normalized);

        return self::sortOrConstraints($normalized);
    }

    private static function trimOuter(string $versionConstraint): string
    {
        return \trim(\str_replace(
            '  ',
            ' ',
            $versionConstraint,
        ));
    }

    private static function normalizeVersionConstraintSeparators(string $versionConstraint): string
    {
        /**
         * @see https://github.com/composer/semver/blob/3.3.2/src/VersionParser.php#L257
         *
         * @var array<int, string> $orConstraints
         */
        $orConstraints = \preg_split(
            '{\s*\|\|?\s*}',
            $versionConstraint,
        );

        return \implode(
            ' || ',
            \array_map(static function (string $orConstraint): string {
                /**
                 * @see https://github.com/composer/semver/blob/3.3.2/src/VersionParser.php#L264
                 *
                 * @var array<int, string> $andConstraints
                 */
                $andConstraints = \preg_split(
                    '{(?<!^|as|[=>< ,]) *(?<!-)[, ](?!-) *(?!,|as|$)}',
                    $orConstraint,
                );

                return \implode(
                    ' ',
                    $andConstraints,
                );
            }, $orConstraints),
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

    private static function sortOrConstraints(string $versionConstraint): string
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

        $orConstraints = \explode(' || ', $versionConstraint);

        $orConstraints = \array_map(static function (string $versionConstraint) use ($sort): string {
            $ranges = \explode(' - ', $versionConstraint);

            $ranges = \array_map(static function (string $range) use ($sort): string {
                if (\str_contains($range, ' as ')) {
                    $andConstraints = [];

                    $temp = \explode(' ', $range);

                    while ([] !== $temp) {
                        if ('as' === $temp[0]) {
                            \array_shift($temp);

                            $andConstraints[\count($andConstraints) - 1] .= ' as ' . \array_shift($temp);
                        } else {
                            $andConstraints[] = \array_shift($temp);
                        }
                    }
                } else {
                    $andConstraints = \explode(' ', $range);
                }

                \usort($andConstraints, $sort);

                return \implode(' ', $andConstraints);
            }, $ranges);

            \usort($ranges, $sort);

            return \implode(' - ', $ranges);
        }, $orConstraints);

        \usort($orConstraints, $sort);

        return \implode(' || ', $orConstraints);
    }
}
