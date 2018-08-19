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

final class InvalidIndentSizeException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $minimumSize;

    public static function fromSizeAndMinimumSize(int $size, int $minimumSize): self
    {
        $exception = new self(\sprintf(
            'Size needs to be greater than %d, but %d is not.',
            $minimumSize - 1,
            $size
        ));

        $exception->size = $size;
        $exception->minimumSize = $minimumSize;

        return $exception;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function minimumSize(): int
    {
        return $this->minimumSize;
    }
}
