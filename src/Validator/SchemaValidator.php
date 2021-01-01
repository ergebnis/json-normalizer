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

    public function validate($data, \stdClass $schema): Result
    {
        $this->validator->reset();

        $this->validator->check(
            $data,
            $schema
        );

        /** @var array $originalErrors */
        $originalErrors = $this->validator->getErrors();

        $errors = \array_map(static function (array $error): string {
            $property = '';

            if (
                \array_key_exists('property', $error)
                && \is_string($error['property'])
                && '' !== \trim($error['property'])
            ) {
                $property = \trim($error['property']);
            }

            $message = '';

            if (
                \array_key_exists('message', $error)
                && \is_string($error['message'])
                && '' !== \trim($error['message'])
            ) {
                $message = \trim($error['message']);
            }

            if ('' === $property) {
                return $message;
            }

            return \sprintf(
                '%s: %s',
                $property,
                $message
            );
        }, $originalErrors);

        $filtered = \array_filter($errors, static function (string $error): bool {
            return '' !== $error;
        });

        return Result::create(...$filtered);
    }
}
