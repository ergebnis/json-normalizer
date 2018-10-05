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

use Localheinz\Json\Printer;

final class IndentNormalizer implements NormalizerInterface
{
    /**
     * @var Format\Indent
     */
    private $indent;

    /**
     * @var Printer\PrinterInterface
     */
    private $printer;

    public function __construct(Format\Indent $indent, Printer\PrinterInterface $printer = null)
    {
        $this->indent = $indent;
        $this->printer = $printer ?: new Printer\Printer();
    }

    public function normalize(Json $json): Json
    {
        $withIndent = $this->printer->print(
            $json->encoded(),
            $this->indent->__toString()
        );

        return Json::fromEncoded($withIndent);
    }
}
