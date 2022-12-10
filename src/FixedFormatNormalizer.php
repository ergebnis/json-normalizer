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

use Ergebnis\Json\Json;

final class FixedFormatNormalizer implements Normalizer
{
    public function __construct(
        private Normalizer $normalizer,
        private Format\Format $format,
        private Format\Formatter $formatter,
    ) {
    }

    public function normalize(Json $json): Json
    {
        return $this->formatter->format(
            $this->normalizer->normalize($json),
            $this->format,
        );
    }
}
