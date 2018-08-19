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

namespace Localheinz\Json\Normalizer\Exception;

final class NormalizedInvalidAccordingToSchemaException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var string
     */
    private $schemaUri;

    public static function fromSchemaUri(string $schemaUri): self
    {
        $exception = new self(\sprintf(
            'Normalized JSON is not valid according to schema "%s".',
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
