<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Vendor\Composer;

use Ergebnis\Json\Normalizer;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

final class ComposerJsonNormalizer implements Normalizer\NormalizerInterface
{
    /**
     * @var Normalizer\NormalizerInterface
     */
    private $normalizer;

    public function __construct(string $schemaUri)
    {
        $this->normalizer = new Normalizer\ChainNormalizer(
            new Normalizer\SchemaNormalizer(
                $schemaUri,
                new SchemaStorage(),
                new Normalizer\Validator\SchemaValidator(new Validator())
            ),
            new BinNormalizer(),
            new ConfigHashNormalizer(),
            new PackageHashNormalizer(),
            new VersionConstraintNormalizer()
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
