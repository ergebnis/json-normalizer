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
     * @var Format\JsonEncodeOptions
     */
    private $jsonEncodeOptions;

    public function __construct(Format\JsonEncodeOptions $jsonEncodeOptions)
    {
        $this->jsonEncodeOptions = $jsonEncodeOptions;
    }

    public function normalize(Json $json): Json
    {
        /** @var string $encodedWithJsonEncodeOptions */
        $encodedWithJsonEncodeOptions = \json_encode(
            $json->decoded(),
            $this->jsonEncodeOptions->value()
        );

        return Json::fromEncoded($encodedWithJsonEncodeOptions);
    }
}
