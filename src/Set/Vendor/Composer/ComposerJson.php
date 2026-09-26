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

namespace Ergebnis\Json\Normalizer\Set\Vendor\Composer;

use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Set;
use Ergebnis\Json\Normalizer\Skip;
use Ergebnis\Json\Pointer;

final class ComposerJson implements Set
{
    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function name(): Set\Name
    {
        return Set\Name::fromString('@composer-json');
    }

    public function rules(): array
    {
        return [
            Rule\Sort\PropertiesBySchema::create(),
            Rule\Sort\PropertiesByName::create(),
            Rule\Prune\EmptyOptionalProperties::create(),
            Rule\Vendor\Composer\Bin\SortElements::create(),
            Rule\Vendor\Composer\Config\SortProperties::create(),
            Rule\Vendor\Composer\Config\SortPropertiesWithWildcards::create(),
            Rule\Vendor\Composer\Repositories\SortFilterElements::create(),
            Rule\Vendor\Composer\Packages\MergeDuplicateExtensions::create(),
            Rule\Vendor\Composer\Packages\SortProperties::create(),
            Rule\Vendor\Composer\VersionConstraint\Trim::create(),
            Rule\Vendor\Composer\VersionConstraint\RemoveExtraSpaces::create(),
            Rule\Vendor\Composer\VersionConstraint\NormalizeSeparators::create(),
            Rule\Vendor\Composer\VersionConstraint\RemoveLeadingV::create(),
            Rule\Vendor\Composer\VersionConstraint\MoveDevAffix::create(),
            Rule\Vendor\Composer\VersionConstraint\ReplaceXWithAsterisk::create(),
            Rule\Vendor\Composer\VersionConstraint\ReplaceWildcardWithTilde::create(),
            Rule\Vendor\Composer\VersionConstraint\ReplaceTildeWithCaret::create(),
        ];
    }

    public function skips(): array
    {
        $orderMatters = Pointer\Specification::anyOf(
            /**
             * First matching allow plugin rule wins.
             *
             * @see https://github.com/composer/composer/blob/2.4.4/src/Composer/Plugin/PluginManager.php#L659-L743
             * @see https://github.com/composer/composer/blob/2.4.4/src/Composer/Plugin/PluginManager.php#L664
             * @see https://github.com/composer/composer/blob/2.4.4/src/Composer/Plugin/PluginManager.php#L684-L688
             * @see https://github.com/composer/composer/blob/2.4.4/src/Composer/Plugin/PluginManager.php#L85
             */
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/config/allow-plugins')),
            /**
             * First matching preferred installation method wins.
             *
             * @see https://github.com/composer/composer/blob/2.4.4/src/Composer/Factory.php#L512-L528
             * @see https://github.com/composer/composer/blob/2.4.4/src/Composer/Downloader/DownloadManager.php#L421-L423
             * @see https://github.com/composer/composer/blob/2.4.4/src/Composer/Downloader/DownloadManager.php#L367-L381
             */
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/config/preferred-install')),
            /**
             * First matching installer path wins.
             *
             * @see https://github.com/composer/installers/blob/v2.2.0/src/Composer/Installers/BaseInstaller.php#L52-L58
             * @see https://github.com/composer/installers/blob/v2.2.0/src/Composer/Installers/BaseInstaller.php#L116-L126
             */
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/extra/installer-paths')),
            /**
             * Patches need to be installed in a specific order.
             *
             * @see https://github.com/cweagans/composer-patches/blob/1.7.2/src/Patches.php#L229-L234
             * @see https://github.com/cweagans/composer-patches/blob/1.7.2/src/Patches.php#L315-L329
             */
            Pointer\Specification::closure(static function (Pointer\JsonPointer $jsonPointer): bool {
                return 1 === \preg_match(
                    '{^/extra/patches/([^/])+$}',
                    $jsonPointer->toJsonString(),
                );
            }),
            /**
             * Repositories need to be iterated in a specific order, but can be an array or an object.
             *
             * @see https://getcomposer.org/doc/04-schema.md#repositories
             * @see https://github.com/composer/composer/blob/2.5.4/res/composer-schema.json#L187-L207
             */
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/repositories')),
            /**
             * Commands need to executed in a specific order.
             *
             * @see https://github.com/symfony/flex/blob/v2.2.3/src/Flex.php#L517-L519
             */
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/scripts/auto-scripts')),
        );

        /**
         * Rules of this set sort these objects by name.
         */
        $sortedByOtherRules = Pointer\Specification::anyOf(
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/config')),
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/conflict')),
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/provide')),
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/replace')),
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/require')),
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/require-dev')),
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/suggest')),
        );

        $sortPropertiesBySchema = Rule\Name::fromString('sort/properties-by-schema');
        $sortPropertiesByName = Rule\Name::fromString('sort/properties-by-name');

        return [
            Skip::create(
                $sortPropertiesBySchema,
                $orderMatters,
            ),
            Skip::create(
                $sortPropertiesBySchema,
                $sortedByOtherRules,
            ),
            Skip::create(
                $sortPropertiesByName,
                $orderMatters,
            ),
            Skip::create(
                $sortPropertiesByName,
                $sortedByOtherRules,
            ),
            Skip::create(
                Rule\Name::fromString('prune/empty-optional-properties'),
                $orderMatters,
            ),
        ];
    }
}
