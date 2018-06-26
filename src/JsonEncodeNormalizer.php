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

final class JsonEncodeNormalizer implements NormalizerInterface
{
    /**
     * @var int
     */
    private $jsonEncodeOptions;

    /**
     * @param int $jsonEncodeOptions
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(int $jsonEncodeOptions)
    {
        if (0 > $jsonEncodeOptions) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid options for json_encode().',
                $jsonEncodeOptions
            ));
        }

        $this->jsonEncodeOptions = $jsonEncodeOptions;
    }

    public function normalize(string $json): string
    {
        if (null === \json_decode($json) && \JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid JSON.',
                $json
            ));
        }

        return \json_encode(
            \json_decode($json),
            $this->jsonEncodeOptions
        );
    }
}
