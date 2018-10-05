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

namespace Localheinz\Json\Normalizer\Format;

use Localheinz\Json\Normalizer\Json;
use Localheinz\Json\Normalizer\JsonInterface;
use Localheinz\Json\Printer;

final class Formatter implements FormatterInterface
{
    /**
     * @var Printer\PrinterInterface
     */
    private $printer;

    public function __construct(Printer\PrinterInterface $printer = null)
    {
        $this->printer = $printer ?: new Printer\Printer();
    }

    public function format(JsonInterface $json, Format $format): JsonInterface
    {
        /** @var string $encoded */
        $encoded = \json_encode(
            $json->decoded(),
            $format->jsonEncodeOptions()
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
