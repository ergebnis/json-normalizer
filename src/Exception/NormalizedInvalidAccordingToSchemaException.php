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

namespace Ergebnis\Json\Normalizer\Exception;

final class NormalizedInvalidAccordingToSchemaException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var string
     */
    private $schemaUri = '';

    /**
     * @var string[]
     */
    private $errors = [];

    public static function fromSchemaUriAndErrors(string $schemaUri, string ...$errors): self
    {
        $exception = new self(\sprintf(
            'Normalized JSON is not valid according to schema "%s".',
            $schemaUri
        ));

        $exception->schemaUri = $schemaUri;
        $exception->errors = $errors;

        return $exception;
    }

    public function schemaUri(): string
    {
        return $this->schemaUri;
    }

    /**
     * @return string[]
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
