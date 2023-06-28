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

namespace Ergebnis\Json\Normalizer\Format;

use Ergebnis\Json\Json;
use Ergebnis\Json\Printer;

final class DefaultFormatter implements Formatter
{
    public function __construct(private readonly Printer\PrinterInterface $printer)
    {
    }

    public function format(
        Json $json,
        Format $format,
    ): Json {
        /** @var string $encoded */
        $encoded = \json_encode(
            $json->decoded(),
            $format->jsonEncodeOptions()->toInt(),
        );

        $printed = $this->printer->print(
            $encoded,
            $format->indent()->toString(),
            $format->newLine()->toString(),
        );

        if (!$format->hasFinalNewLine()) {
            return Json::fromString($printed);
        }

        return Json::fromString($printed . $format->newLine()->toString());
    }
}
