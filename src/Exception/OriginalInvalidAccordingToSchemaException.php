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

final class OriginalInvalidAccordingToSchemaException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var string
     */
    private $schemaUri = '';

    public static function fromSchemaUri(string $schemaUri): self
    {
        $exception = new self(\sprintf(
            'Original JSON is not valid according to schema "%s".',
            $schemaUri
        ));

        $exception->schemaUri = $schemaUri;

        return $exception;
    }

    public function schemaUri(): string
    {
        return $this->schemaUri;
    }
}
