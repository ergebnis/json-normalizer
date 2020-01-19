<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Validator;

interface SchemaValidatorInterface
{
    /**
     * @param null|array<mixed>|bool|float|int|\stdClass|string $data
     * @param \stdClass                                         $schema
     *
     * @return bool
     */
    public function isValid($data, \stdClass $schema): bool;
}
