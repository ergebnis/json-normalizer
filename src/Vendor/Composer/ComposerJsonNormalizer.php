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
                    Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/config/allow-plugins')),
                    Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/extra/installer-paths')),
                    Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/scripts/auto-scripts')),
                ),
            ),
            new BinNormalizer(),
            new PackageHashNormalizer(),
            new VersionConstraintNormalizer(),
        );
    }

    public function normalize(Json $json): Json
    {
        if (!\is_object($json->decoded())) {
            return $json;
        }

        return $this->normalizer->normalize($json);
    }
}
