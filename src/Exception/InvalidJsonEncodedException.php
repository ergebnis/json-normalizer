<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Exception;

final class InvalidJsonEncodedException extends \InvalidArgumentException implements ExceptionInterface
{
    private string $encoded = '';

    public static function fromEncoded(string $encoded): self
    {
        $exception = new self(\sprintf(
            '"%s" is not valid JSON.',
            $encoded,
        ));

        $exception->encoded = $encoded;

        return $exception;
    }

    public function encoded(): string
    {
        return $this->encoded;
    }
}
