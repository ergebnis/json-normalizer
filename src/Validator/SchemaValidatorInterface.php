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

namespace Ergebnis\Json\Normalizer\Validator;

interface SchemaValidatorInterface
{
    /**
     * @deprecated will be removed in 0.13.0
     *
     * @param null|array<mixed>|bool|float|int|\stdClass|string $data
     */
    public function isValid($data, \stdClass $schema): bool;

    /**
     * @param null|array<mixed>|bool|float|int|\stdClass|string $data
     */
    public function validate($data, \stdClass $schema): Result;
}
