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

final class InvalidJsonDecodedException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @var mixed
     */
    private $decoded;

    public static function fromDecoded($decoded): self
    {
        $exception = new self('The provided data cannot be encoded to JSON.');

        $exception->decoded = $decoded;

        return $exception;
    }

    /**
     * @return mixed
     */
    public function decoded()
    {
        return $this->decoded;
    }
}
