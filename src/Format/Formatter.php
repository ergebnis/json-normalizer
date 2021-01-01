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

namespace Ergebnis\Json\Normalizer\Format;

use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Printer;

final class Formatter implements FormatterInterface
{
    /**
     * @var Printer\PrinterInterface
     */
    private $printer;

    public function __construct(Printer\PrinterInterface $printer)
    {
        $this->printer = $printer;
    }

    public function format(Json $json, Format $format): Json
    {
        /** @var string $encoded */
        $encoded = \json_encode(
            $json->decoded(),
            $format->jsonEncodeOptions()->value()
        );

        $printed = $this->printer->print(
            $encoded,
            (string) $format->indent(),
            (string) $format->newLine()
        );

        if (!$format->hasFinalNewLine()) {
            return Json::fromEncoded($printed);
        }

        return Json::fromEncoded($printed . $format->newLine());
    }
}
