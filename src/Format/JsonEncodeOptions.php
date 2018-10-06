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

namespace Localheinz\Json\Normalizer\Format;

use Localheinz\Json\Normalizer\Exception;

final class JsonEncodeOptions
{
    /**
     * @var int
     */
    private $value;

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @param int $value
     *
     * @throws Exception\InvalidJsonEncodeOptionsException
     *
     * @return JsonEncodeOptions
     */
    public static function fromInt(int $value): self
    {
        if (0 > $value) {
            throw Exception\InvalidJsonEncodeOptionsException::fromJsonEncodeOptions($value);
        }

        return new self($value);
    }

    public function value(): int
    {
        return $this->value;
    }
}
