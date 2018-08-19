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

final class InvalidNewLineStringException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @var string
     */
    private $string;

    public static function fromString(string $string): self
    {
        $exception = new self(\sprintf(
            '"%s" is not a valid new-line character sequence.',
            $string
        ));

        $exception->string = $string;

        return $exception;
    }

    public function string(): string
    {
        return $this->string;
    }
}
