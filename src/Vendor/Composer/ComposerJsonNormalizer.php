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

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Pointer;
use Ergebnis\Json\SchemaValidator;
use JsonSchema\SchemaStorage;

final class ComposerJsonNormalizer implements Normalizer\Normalizer
{
    private Normalizer\Normalizer $normalizer;

    public function __construct(string $schemaUri)
    {
        $this->normalizer = new Normalizer\ChainNormalizer(
            new Normalizer\SchemaNormalizer(
                $schemaUri,
                new SchemaStorage(),
                new SchemaValidator\SchemaValidator(),
                Pointer\Specification::anyOf(
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
                        return 1 === \preg_match('{^\/extra\/patches\/([^/])+$}', $jsonPointer->toJsonString());
                    }),
                    /**
                     * Commands need to executed in a specific order.
                     *
                     * @see https://github.com/symfony/flex/blob/v2.2.3/src/Flex.php#L517-L519
                     */
                    Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/scripts/auto-scripts')),
                ),
            ),
            self::binNormalizer(),
            new PackageHashNormalizer(),
            new VersionConstraintNormalizer(),
            new Normalizer\WithFinalNewLineNormalizer(),
        );
    }

    public function normalize(Json $json): Json
    {
        return $this->normalizer->normalize($json);
    }

    private static function binNormalizer(): Normalizer\Normalizer
    {
        return new class() implements Normalizer\Normalizer {
            public function normalize(Json $json): Json
            {
                /** @var object $decoded */
                $decoded = $json->decoded();

                if (!\property_exists($decoded, 'bin')) {
                    return $json;
                }

                if (!\is_array($decoded->bin)) {
                    return $json;
                }

                $bin = $decoded->bin;

                \sort($bin);

                $decoded->bin = $bin;

                /** @var string $encoded */
                $encoded = \json_encode(
                    $decoded,
                    Normalizer\Format\JsonEncodeOptions::default()->toInt(),
                );

                return Json::fromString($encoded);
            }
        };
    }
}
