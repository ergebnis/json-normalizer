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

final class IndentNormalizer implements Normalizer
{
    public function __construct(
        private readonly Format\Indent $indent,
        private readonly Printer\PrinterInterface $printer,
    ) {
    }

    public function normalize(Json $json): Json
    {
        $withIndent = $this->printer->print(
            $json->encoded(),
            $this->indent->toString(),
        );

        return Json::fromString($withIndent);
    }
}
