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

namespace Ergebnis\Json\Normalizer\Exception;

final class OriginalInvalidAccordingToSchemaException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var string
     */
    private $schemaUri = '';

    /**
     * @phpstan-var list<string>
     * @psalm-var list<string>
     *
     * @var array<int, string>
     */
    private $errors = [];

    public static function fromSchemaUriAndErrors(string $schemaUri, string ...$errors): self
    {
        $exception = new self(\sprintf(
            'Original JSON is not valid according to schema "%s".',
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
     * @phpstan-return list<string>
     * @psalm-return list<string>
     *
     * @return array<int, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
