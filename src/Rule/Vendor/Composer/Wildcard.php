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

namespace Ergebnis\Json\Normalizer\Rule\Vendor\Composer;

/**
 * When sorting with wildcards, special care needs to be taken.
 *
 * @internal
 *
 * @see https://github.com/ergebnis/json-normalizer/pull/775#issuecomment-1346095415
 * @see https://github.com/composer/composer/blob/2.6.5/src/Composer/Plugin/PluginManager.php#L85-L86
 * @see https://github.com/composer/composer/blob/2.6.5/src/Composer/Plugin/PluginManager.php#L626-L646
 * @see https://github.com/composer/composer/blob/2.6.5/src/Composer/Package/BasePackage.php#L252-L257
 * @see https://github.com/composer/composer/blob/2.6.5/src/Composer/Plugin/PluginManager.php#L687-L691
 */
final class Wildcard
{
    /**
     * We cannot reliably sort when there is a wildcard other than at the end of a value.
     */
    public static function containsWildcardNotAtEnd(string $value): bool
    {
        return false !== \strpos(
            \rtrim(
                $value,
                '*',
            ),
            '*',
        );
    }

    /**
     * Any value with a wildcard needs to be the last entry in its group.
     */
    public static function compare(
        string $one,
        string $two
    ): int {
        return \strcmp(
            \str_replace(
                '*',
                '~',
                $one,
            ),
            \str_replace(
                '*',
                '~',
                $two,
            ),
        );
    }
}
