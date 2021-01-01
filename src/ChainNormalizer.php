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

namespace Ergebnis\Json\Normalizer;

final class ChainNormalizer implements NormalizerInterface
{
    /**
     * @phpstan-var list<NormalizerInterface>
     * @psalm-var list<NormalizerInterface>
     *
     * @var array<int, NormalizerInterface>
     */
    private $normalizers;

    public function __construct(NormalizerInterface ...$normalizers)
    {
        $this->normalizers = $normalizers;
    }

    public function normalize(Json $json): Json
    {
        foreach ($this->normalizers as $normalizer) {
            $json = $normalizer->normalize($json);
        }

        return $json;
    }
}
