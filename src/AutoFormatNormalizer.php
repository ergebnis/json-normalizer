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

final class AutoFormatNormalizer implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var Format\SnifferInterface
     */
    private $sniffer;

    /**
     * @var Format\FormatterInterface
     */
    private $formatter;

    public function __construct(
        NormalizerInterface $normalizer,
        Format\SnifferInterface $sniffer = null,
        Format\FormatterInterface $formatter = null
    ) {
        $this->normalizer = $normalizer;
        $this->sniffer = $sniffer ?: new Format\Sniffer();
        $this->formatter = $formatter ?: new Format\Formatter();
    }

    public function normalize(string $json): string
    {
        if (null === \json_decode($json) && JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid JSON.',
                $json
            ));
        }

        $format = $this->sniffer->sniff($json);

        $normalized = $this->normalizer->normalize($json);

        return $this->formatter->format(
            $normalized,
            $format
        );
    }
}
