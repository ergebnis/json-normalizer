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

use Ergebnis\Json\Normalizer;

return Normalizer\Format\Format::create(
    Normalizer\Format\JsonEncodeOptions::fromInt(\JSON_UNESCAPED_SLASHES),
    Normalizer\Format\Indent::fromSizeAndStyle(
        2,
        'space',
    ),
    Normalizer\Format\NewLine::fromString(\PHP_EOL),
    true,
);
