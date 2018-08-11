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

    public function normalize(JsonInterface $json): JsonInterface
    {
        return $this->formatter->format(
            $this->normalizer->normalize($json),
            $this->sniffer->sniff($json)
        );
    }
}
