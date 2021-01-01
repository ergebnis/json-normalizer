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

final class AutoFormatNormalizer implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var Format\FormatterInterface
     */
    private $formatter;

    public function __construct(NormalizerInterface $normalizer, Format\FormatterInterface $formatter)
    {
        $this->normalizer = $normalizer;
        $this->formatter = $formatter;
    }

    public function normalize(Json $json): Json
    {
        return $this->formatter->format(
            $this->normalizer->normalize($json),
            $json->format()
        );
    }
}
