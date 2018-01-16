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

use Localheinz\Json\Printer;

final class FixedFormatNormalizer implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var Format\FormatInterface
     */
    private $format;

    /**
     * @var Printer\PrinterInterface
     */
    private $printer;

    public function __construct(
        NormalizerInterface $normalizer,
        Format\FormatInterface $format,
        Printer\PrinterInterface $printer = null
    ) {
        $this->normalizer = $normalizer;
        $this->format = $format;
        $this->printer = $printer ?: new Printer\Printer();
    }

    public function normalize(string $json): string
    {
        if (null === \json_decode($json) && JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid JSON.',
                $json
            ));
        }

        $normalized = $this->normalizer->normalize($json);

        $encoded = \json_encode(
            \json_decode($normalized),
            $this->format->jsonEncodeOptions()
        );

        $printed = $this->printer->print(
            $encoded,
            $this->format->indent()
        );

        if (!$this->format->hasFinalNewLine()) {
            return $printed;
        }

        return $printed . PHP_EOL;
    }
}
