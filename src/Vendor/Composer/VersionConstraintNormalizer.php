<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
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

    /**
     * Ordered from the most stable to the least stable stability modifier.
     */
    private const STABILITY_MODIFIERS = [
        'stable',
        'RC',
        'beta',
        'alpha',
        'dev',
    ];
    private const STABILITY_MODIFIER_REGEX = '{@(stable|RC|beta|alpha|dev)(?=$|[\s,|])}i';
    private Semver\VersionParser $versionParser;

    public function __construct(Semver\VersionParser $versionParser)
    {
        $this->versionParser = $versionParser;
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
            $packages = (array) $value;

            if ([] === $packages) {
                continue;
            }

            $decoded->{$name} = \array_map(function (string $versionConstraint): string {
                $versionConstraint = self::trim($versionConstraint);
                $versionConstraint = self::removeExtraSpaces($versionConstraint);

                try {
                    $this->versionParser->parseConstraints($versionConstraint);
                } catch (\UnexpectedValueException $exception) {
                    return $versionConstraint;
                }

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
        $stabilityModifier = self::findLeastStableStabilityModifier($versionConstraint);

        if ('' !== $stabilityModifier) {
            $versionConstraint = self::removeStabilityModifiers($versionConstraint);
        }

        $versionConstraint = self::trim($versionConstraint);
        $versionConstraint = self::normalizeVersionConstraintSeparators($versionConstraint);
        $versionConstraint = self::removeLeadingVersionPrefix($versionConstraint);
        $versionConstraint = self::assertDevPrefixSuffixPosition($versionConstraint);
        $versionConstraint = self::replaceWildcardXWithAsterisk($versionConstraint);
        $versionConstraint = self::replaceWildcardWithTilde($versionConstraint);
        $versionConstraint = self::replaceTildeWithCaret($versionConstraint);
        $versionConstraint = self::removeDuplicateVersionConstraints($versionConstraint);
        $versionConstraint = self::removeUselessInlineAliases($versionConstraint);
        $versionConstraint = self::sortVersionConstraints($versionConstraint);
        $versionConstraint = self::removeOverlappingVersionConstraints($versionConstraint);

        return self::appendStabilityModifier(
            self::trim($versionConstraint),
            $stabilityModifier,
        );
    }

    /**
     * Appends the stability modifier to the last version constraint, as Composer documents stability modifiers as suffixes only.
     *
     * @see https://getcomposer.org/doc/04-schema.md#package-links
     */
    private static function appendStabilityModifier(
        string $versionConstraint,
        string $stabilityModifier
    ): string {
        if ('' === $versionConstraint) {
            return $stabilityModifier;
        }

        $orConstraints = self::splitIntoOrConstraints($versionConstraint);

        $orConstraints[\count($orConstraints) - 1] .= $stabilityModifier;

        return self::joinOrConstraints(...$orConstraints);
    }

    /**
     * Composer applies the least stable of the stability modifiers in a version constraint to the package, not to the version constraint it is attached to.
     *
     * Returns an empty string when the version constraint does not contain a stability modifier, or when its stability modifiers must not be moved: when it contains an inline alias, as moving a stability modifier would break the inline alias, or when it contains a stability modifier on its own as an alternative or anywhere but at the beginning, as Composer treats such a stability modifier as a version constraint matching any version.
     *
     * @see https://getcomposer.org/doc/04-schema.md#package-links
     * @see https://github.com/composer/composer/blob/2.10.3/src/Composer/Package/Loader/RootPackageLoader.php#L245-L292
     */
    private static function findLeastStableStabilityModifier(string $versionConstraint): string
    {
        if (1 === \preg_match('{\s+as\s+}', $versionConstraint)) {
            return '';
        }

        if (1 === \preg_match('{[\s,|]@}', $versionConstraint)) {
            return '';
        }

        if (1 === \preg_match('{^@\w+\s*\|}', $versionConstraint)) {
            return '';
        }

        \preg_match_all(
            self::STABILITY_MODIFIER_REGEX,
            $versionConstraint,
            $matches,
        );

        $leastStableStabilityModifier = '';

        foreach (self::STABILITY_MODIFIERS as $stabilityModifier) {
            foreach ($matches[1] as $match) {
                if (0 === \strcasecmp($stabilityModifier, $match)) {
                    $leastStableStabilityModifier = '@' . $stabilityModifier;
                }
            }
        }

        return $leastStableStabilityModifier;
    }

    private static function removeStabilityModifiers(string $versionConstraint): string
    {
        return \preg_replace(
            self::STABILITY_MODIFIER_REGEX,
            '',
            $versionConstraint,
        );
    }

    private static function trim(string $versionConstraint): string
    {
        return \trim($versionConstraint);
    }

    private static function removeExtraSpaces(string $versionConstraint): string
    {
        return \preg_replace(
            '/ +/',
            ' ',
            $versionConstraint,
        );
    }

    private static function normalizeVersionConstraintSeparators(string $versionConstraint): string
    {
        $orConstraints = self::splitIntoOrConstraints($versionConstraint);

        return self::joinOrConstraints(...\array_map(static function (string $orConstraint): string {
            $andConstraints = self::splitIntoAndConstraints($orConstraint);

            return self::joinAndConstraints(...$andConstraints);
        }, $orConstraints));
    }

    private static function replaceWildcardXWithAsterisk(string $versionConstraint): string
    {
        // '1.x.x' -> '1.*'
        $versionConstraint = self::applyRegularExpressionReplacementToVersionsInTurn(
            $versionConstraint,
            '{^(\d+)\.[xX]\.[xX]$}',
            '$1.*',
        );

        // '1.x' -> '1.*'
        $versionConstraint = self::applyRegularExpressionReplacementToVersionsInTurn(
            $versionConstraint,
            '{^(\d+)\.[xX]$}',
            '$1.*',
        );

        // 'x' -> '*'
        return self::applyRegularExpressionReplacementToVersionsInTurn(
            $versionConstraint,
            '{^[xX]$}',
            '*',
        );
    }

    private static function replaceWildcardWithTilde(string $versionConstraint): string
    {
        return self::applyRegularExpressionReplacementToVersionsInTurn(
            $versionConstraint,
            '{^(\d+(?:\.\d+)*)\.[*xX]$}',
            '~$1.0',
        );
    }

    /**
     * Replaces a tilde version range with a caret version range only where both are equivalent:
     *
     * - with a major version only, for example, ~0 and ^0, or ~1 and ^1
     * - with a major version other than 0 and a minor version, for example, ~1.2 and ^1.2
     *
     * With a major version of 0 and a minor version, they are not equivalent: ~0.1 allows >=0.1 <1.0, while ^0.1 allows >=0.1 <0.2.
     *
     * With a patch version, they are either not equivalent (~1.2.3 and ^1.2.3), or the tilde version range is more explicit (~0.1.2 and ^0.1.2).
     *
     * @see https://getcomposer.org/doc/articles/versions.md#tilde-version-range-
     * @see https://getcomposer.org/doc/articles/versions.md#caret-version-range-
     */
    private static function replaceTildeWithCaret(string $versionConstraint): string
    {
        return self::applyRegularExpressionReplacementToVersionsInTurn(
            $versionConstraint,
            '{^~(\d+|[1-9]\d*\.\d+)$}',
            '^$1',
        );
    }

    private static function removeDuplicateVersionConstraints(string $versionConstraint): string
    {
        $orConstraints = self::splitIntoOrConstraints($versionConstraint);

        return self::joinOrConstraints(...\array_unique(\array_map(static function (string $orConstraint): string {
            $andConstraints = self::splitIntoAndConstraints($orConstraint);

            return self::joinAndConstraints(...\array_unique($andConstraints));
        }, $orConstraints)));
    }

    private static function removeLeadingVersionPrefix(string $versionConstraint): string
    {
        return self::applyRegularExpressionReplacementToVersionsInTurn(
            $versionConstraint,
            '{^(|[!<>]=|[~<>^])v(\d+.*(?<!-dev))$}',
            '$1$2',
        );
    }

    private static function assertDevPrefixSuffixPosition(string $versionConstraint): string
    {
        $split = \explode(
            ' ',
            $versionConstraint,
        );

        foreach ($split as &$part) {
            if (\strlen($part) <= 4) {
                continue;
            }

            if (\strpos($part, 'dev-') === 0) {
                $branch = \substr($part, 4);
            } elseif (\substr($part, -4) === '-dev') {
                $branch = \substr($part, 0, -4);
            } else {
                continue;
            }

            /**
             * @see https://github.com/composer/semver/blob/3.4.0/src/VersionParser.php#L216
             */
            if (1 === \preg_match('{^v?\d+(\.(?:\d+|[xX*]))?(\.(?:\d+|[xX*]))?(\.(?:\d+|[xX*]))?$}i', $branch)) {
                $part = $branch . '-dev';
            } else {
                $part = 'dev-' . $branch;
            }
        }

        return \implode(
            ' ',
            $split,
        );
    }

    private static function removeOverlappingVersionConstraints(string $versionConstraint): string
    {
        $orConstraints = self::splitIntoOrConstraints($versionConstraint);

        $regex = '{^[~^]?\d+(?:\.\d+)*$}';

        $versionParser = new Semver\VersionParser();

        $count = \count($orConstraints);

        for ($i = 0; $i < $count; ++$i) {
            $a = $orConstraints[$i];

            if (!\is_string($a)) {
                continue;
            }

            if ('*' === $a) {
                return $a;
            }

            if (1 !== \preg_match($regex, $a)) {
                continue;
            }

            for ($j = $i + 1; $j < $count; ++$j) {
                $b = $orConstraints[$j];

                if (!\is_string($b)) {
                    continue;
                }

                if (1 !== \preg_match($regex, $b)) {
                    continue;
                }

                $constraintA = $versionParser->parseConstraints($a);
                $constraintB = $versionParser->parseConstraints($b);

                $aIsSubsetOfB = Semver\Intervals::isSubsetOf(
                    $constraintA,
                    $constraintB,
                );
                $bIsSubsetOfA = Semver\Intervals::isSubsetOf(
                    $constraintB,
                    $constraintA,
                );

                if (
                    !$aIsSubsetOfB
                    && !$bIsSubsetOfA
                ) {
                    continue;
                }

                if (!$bIsSubsetOfA) {
                    $orConstraints[$i] = null;
                } elseif (!$aIsSubsetOfB) {
                    $orConstraints[$j] = null;
                } elseif (
                    '^' !== $a[0]
                    && '^' === $b[0]
                ) {
                    $orConstraints[$i] = null;
                } else {
                    $orConstraints[$j] = null;
                }
            }
        }

        return self::joinOrConstraints(...\array_filter($orConstraints, static function (?string $orConstraint): bool {
            return \is_string($orConstraint);
        }));
    }

    private static function removeUselessInlineAliases(string $normalized): string
    {
        return \preg_replace_callback(
            '{(\S+)\s+as\s+(\S+)}',
            static function (array $matches): string {
                if ($matches[1] === $matches[2]) {
                    return $matches[1];
                }

                return $matches[0];
            },
            $normalized,
        );
    }

    private static function sortVersionConstraints(string $versionConstraint): string
    {
        $normalize = static function (string $versionConstraint): string {
            return \trim($versionConstraint, '<>=!~^');
        };

        $sort = static function (string $a, string $b) use ($normalize): int {
            return \strnatcmp(
                $normalize($a),
                $normalize($b),
            );
        };

        $orConstraints = self::splitIntoOrConstraints($versionConstraint);

        $orConstraints = \array_map(static function (string $orConstraint) use ($sort): string {
            $andConstraints = self::splitIntoAndConstraints($orConstraint);

            \usort($andConstraints, $sort);

            return self::joinAndConstraints(...$andConstraints);
        }, $orConstraints);

        \usort($orConstraints, $sort);

        return self::joinOrConstraints(...$orConstraints);
    }

    /**
     * @see https://github.com/composer/semver/blob/3.3.2/src/VersionParser.php#L257
     *
     * @return list<string>
     */
    private static function splitIntoOrConstraints(string $versionConstraint): array
    {
        return \preg_split(
            '{\s*\|\|?\s*}',
            $versionConstraint,
        );
    }

    private static function joinOrConstraints(string ...$orConstraints): string
    {
        return \implode(
            ' || ',
            $orConstraints,
        );
    }

    /**
     * @see https://github.com/composer/semver/blob/3.3.2/src/VersionParser.php#L264
     *
     * @return list<string>
     */
    private static function splitIntoAndConstraints(string $orConstraint): array
    {
        return \preg_split(
            '{(?<!^|as|[=>< ,]) *(?<!-)[, ](?!-) *(?!,|as|$)}',
            $orConstraint,
        );
    }

    private static function joinAndConstraints(string ...$andConstraints): string
    {
        return \implode(
            ' ',
            $andConstraints,
        );
    }

    /**
     * @param non-empty-string $find
     */
    private static function applyRegularExpressionReplacementToVersionsInTurn(string $versionConstraint, string $find, string $replace): string
    {
        $split = \explode(
            ' ',
            $versionConstraint,
        );

        foreach ($split as &$part) {
            $part = \preg_replace(
                $find,
                $replace,
                $part,
            );
        }

        return \implode(
            ' ',
            $split,
        );
    }
}
