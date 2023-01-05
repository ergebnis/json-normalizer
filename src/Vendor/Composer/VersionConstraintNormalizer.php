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
        $normalized = \trim(\str_replace(
            '  ',
            ' ',
            $versionConstraint,
        ));

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

        return \trim($normalized);
    }
}
