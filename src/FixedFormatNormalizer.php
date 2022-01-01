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

namespace Ergebnis\Json\Normalizer;

final class FixedFormatNormalizer implements NormalizerInterface
{
    private NormalizerInterface $normalizer;
    private Format\Format $format;
    private Format\FormatterInterface $formatter;

    public function __construct(
        NormalizerInterface $normalizer,
        Format\Format $format,
        Format\FormatterInterface $formatter
    ) {
        $this->normalizer = $normalizer;
        $this->format = $format;
        $this->formatter = $formatter;
    }

    public function normalize(Json $json): Json
    {
        return $this->formatter->format(
            $this->normalizer->normalize($json),
            $this->format,
        );
    }
}
