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

final class InvalidJsonEncodeOptionsException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @var int
     */
    private $jsonEncodeOptions;

    public static function fromJsonEncodeOptions(int $jsonEncodeOptions): self
    {
        $exception = new self(\sprintf(
            '"%s" is not valid options for json_encode().',
            $jsonEncodeOptions
        ));

        $exception->jsonEncodeOptions = $jsonEncodeOptions;

        return $exception;
    }

    public function jsonEncodeOptions(): int
    {
        return $this->jsonEncodeOptions;
    }
}
