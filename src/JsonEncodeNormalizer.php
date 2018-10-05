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
     * @throws Exception\InvalidJsonEncodeOptionsException
     */
    public function __construct(int $jsonEncodeOptions)
    {
        if (0 > $jsonEncodeOptions) {
            throw Exception\InvalidJsonEncodeOptionsException::fromJsonEncodeOptions($jsonEncodeOptions);
        }

        $this->jsonEncodeOptions = $jsonEncodeOptions;
    }

    public function normalize(Json $json): Json
    {
        /** @var string $encodedWithJsonEncodeOptions */
        $encodedWithJsonEncodeOptions = \json_encode(
            $json->decoded(),
            $this->jsonEncodeOptions
        );

        return Json::fromEncoded($encodedWithJsonEncodeOptions);
    }
}
