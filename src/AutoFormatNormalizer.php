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

final class AutoFormatNormalizer implements Normalizer
{
    public function __construct(
        private Normalizer $normalizer,
        private Format\Formatter $formatter,
    ) {
    }

    public function normalize(Json $json): Json
    {
        $format = Format\Format::fromJson($json);

        return $this->formatter->format(
            $this->normalizer->normalize($json),
            $format,
        );
    }
}
