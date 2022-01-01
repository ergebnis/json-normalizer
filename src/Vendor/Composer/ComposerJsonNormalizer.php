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

use Ergebnis\Json\Normalizer;
use Ergebnis\Json\SchemaValidator;
use JsonSchema\SchemaStorage;

final class ComposerJsonNormalizer implements Normalizer\NormalizerInterface
{
    private Normalizer\NormalizerInterface $normalizer;

    public function __construct(string $schemaUri)
    {
        $this->normalizer = new Normalizer\ChainNormalizer(
            new Normalizer\SchemaNormalizer(
                $schemaUri,
                new SchemaStorage(),
                new SchemaValidator\SchemaValidator(),
            ),
            new BinNormalizer(),
            new ConfigHashNormalizer(),
            new PackageHashNormalizer(),
            new VersionConstraintNormalizer(),
        );
    }

    public function normalize(Normalizer\Json $json): Normalizer\Json
    {
        if (!\is_object($json->decoded())) {
            return $json;
        }

        return $this->normalizer->normalize($json);
    }
}
