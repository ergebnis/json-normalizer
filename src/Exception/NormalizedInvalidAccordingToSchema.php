<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Exception;

final class NormalizedInvalidAccordingToSchema extends \RuntimeException implements Exception
{
    private string $schemaUri = '';

    /**
     * @phpstan-var list<string>
     *
     * @psalm-var list<string>
     *
     * @var array<int, string>
     */
    private array $errors = [];

    public static function fromSchemaUriAndErrors(
        string $schemaUri,
        string ...$errors,
    ): self {
        $exception = new self(\sprintf(
            'Normalized JSON is not valid according to schema "%s".',
            $schemaUri,
        ));

        $exception->schemaUri = $schemaUri;
        // @infection-ignore-all UnwrapArrayValues
        $exception->errors = \array_values($errors);

        return $exception;
    }

    public function schemaUri(): string
    {
        return $this->schemaUri;
    }

    /**
     * @phpstan-return list<string>
     *
     * @psalm-return list<string>
     *
     * @return array<int, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
