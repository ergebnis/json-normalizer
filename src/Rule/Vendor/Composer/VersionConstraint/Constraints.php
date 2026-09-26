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

namespace Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint;

use Composer\Semver;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;

/**
 * @internal
 */
final class Constraints
{
    public static function target(): Rule\Target
    {
        return Rule\Target::create(
            Pointer\Specification::closure(static function (Pointer\JsonPointer $jsonPointer): bool {
                return 1 === \preg_match(
                    '{^/(conflict|provide|replace|require|require-dev)/[^/]+$}',
                    $jsonPointer->toJsonString(),
                );
            }),
            Parser\Node\StringNode::class,
        );
    }

    public static function isParsable(string $versionConstraint): bool
    {
        $versionParser = new Semver\VersionParser();

        try {
            $versionParser->parseConstraints($versionConstraint);
        } catch (\UnexpectedValueException $exception) {
            return false;
        }

        return true;
    }

    /**
     * @see https://github.com/composer/semver/blob/3.3.2/src/VersionParser.php#L257
     *
     * @return list<string>
     */
    public static function splitIntoOrConstraints(string $versionConstraint): array
    {
        $constraints = \preg_split(
            '{\s*\|\|?\s*}',
            $versionConstraint,
        );

        if (!\is_array($constraints)) {
            return [
                $versionConstraint,
            ];
        }

        return $constraints;
    }

    public static function joinOrConstraints(string ...$orConstraints): string
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
    public static function splitIntoAndConstraints(string $orConstraint): array
    {
        $constraints = \preg_split(
            '{(?<!^|as|[=>< ,]) *(?<!-)[, ](?!-) *(?!,|as|$)}',
            $orConstraint,
        );

        if (!\is_array($constraints)) {
            return [
                $orConstraint,
            ];
        }

        return $constraints;
    }

    public static function joinAndConstraints(string ...$andConstraints): string
    {
        return \implode(
            ' ',
            $andConstraints,
        );
    }

    /**
     * @param non-empty-string $find
     */
    public static function applyToVersionsInTurn(
        string $versionConstraint,
        string $find,
        string $replace
    ): string {
        $versions = \explode(
            ' ',
            $versionConstraint,
        );

        $replaced = [];

        foreach ($versions as $version) {
            $replaced[] = (string) \preg_replace(
                $find,
                $replace,
                $version,
            );
        }

        return \implode(
            ' ',
            $replaced,
        );
    }
}
