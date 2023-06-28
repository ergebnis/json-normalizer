<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer;

use Ergebnis\Json\Json;
use Ergebnis\Json\Printer;

final class FormatNormalizer implements Normalizer
{
    public function __construct(
        private readonly Printer\PrinterInterface $printer,
        private readonly Format\Format $format,
    ) {
    }

    public function normalize(Json $json): Json
    {
        $normalized = $this->printer->print(
            \json_encode(
                $json->decoded(),
                $this->format->jsonEncodeOptions()->toInt(),
            ),
            $this->format->indent()->toString(),
            $this->format->newLine()->toString(),
        );

        if (!$this->format->hasFinalNewLine()) {
            return Json::fromString($normalized);
        }

        return Json::fromString($normalized . $this->format->newLine()->toString());
    }
}
