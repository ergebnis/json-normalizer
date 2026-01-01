<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

use Ergebnis\Json\Normalizer\Format;

require_once __DIR__ . '/../vendor/autoload.php';

$schemaFile = __DIR__ . '/../test/Fixture/Vendor/Composer/schema.json';

$schema = \json_decode(
    \file_get_contents($schemaFile),
    false,
);

$schema->additionalProperties = true;
$schema->required = [];

\file_put_contents($schemaFile, \json_encode(
    $schema,
    Format\JsonEncodeOptions::default()->toInt(),
));
