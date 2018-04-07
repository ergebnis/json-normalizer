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

    public function format(string $json, FormatInterface $format): string
    {
        $decoded = \json_decode($json);

        if (null === $decoded && JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid JSON.',
                $json
            ));
        }

        $encoded = \json_encode(
            $decoded,
            $format->jsonEncodeOptions()
        );

        $printed = $this->printer->print(
            $encoded,
            $format->indent(),
            $format->newLine()
        );

        if (!$format->hasFinalNewLine()) {
            return $printed;
        }

        return $printed . $format->newLine();
    }
}
