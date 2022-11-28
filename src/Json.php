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

namespace Ergebnis\Json\Normalizer;

/**
 * @psalm-immutable
 */
final class Json
{
    /**
     * @param null|array<mixed>|bool|float|int|object|string $decoded
     */
    private function __construct(
        private string $encoded,
        private $decoded,
    ) {
    }

    /**
     * @throws Exception\InvalidJsonEncoded
     */
    public static function fromEncoded(string $encoded): self
    {
        try {
            $decoded = \json_decode(
                $encoded,
                false,
                512,
                \JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException) {
            throw Exception\InvalidJsonEncoded::fromEncoded($encoded);
        }

        return new self(
            $encoded,
            $decoded,
        );
    }

    /**
     * Returns the decoded JSON value.
     *
     * @return null|array<mixed>|bool|float|int|object|string
     */
    public function decoded()
    {
        return $this->decoded;
    }

    /**
     * Returns the original JSON value.
     */
    public function encoded(): string
    {
        return $this->encoded;
    }

    /**
     * Returns the original JSON value.
     */
    public function toString(): string
    {
        return $this->encoded;
    }
}
