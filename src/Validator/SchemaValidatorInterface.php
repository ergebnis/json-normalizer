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

namespace Localheinz\Json\Normalizer\Validator;

interface SchemaValidatorInterface
{
    /**
     * @param null|array|bool|float|int|\stdClass|string $data
     * @param \stdClass                                  $schema
     *
     * @return bool
     */
    public function isValid($data, \stdClass $schema): bool;
}
