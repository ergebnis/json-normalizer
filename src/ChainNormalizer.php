<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer;

final class ChainNormalizer implements NormalizerInterface
{
    /**
     * @var NormalizerInterface[]
     */
    private $normalizers;

    public function __construct(NormalizerInterface ...$normalizers)
    {
        $this->normalizers = $normalizers;
    }

    public function normalize(string $json): string
    {
        if (null === \json_decode($json) && JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid JSON.',
                $json
            ));
        }

        foreach ($this->normalizers as $normalizer) {
            $json = $normalizer->normalize($json);
        }

        return $json;
    }
}
