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
