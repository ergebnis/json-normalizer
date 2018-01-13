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

use JsonSchema\Validator;

final class SchemaValidator implements SchemaValidatorInterface
{
    /**
     * @var Validator
     */
    private $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function isValid($data, \stdClass $schema): bool
    {
        $this->validator->reset();

        $this->validator->check(
            $data,
            $schema
        );

        return $this->validator->isValid();
    }
}
