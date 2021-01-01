<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer;

final class Json
{
    /**
     * @var string
     */
    private $encoded;

    /**
     * @var null|array<mixed>|bool|float|int|\stdClass|string
     */
    private $decoded;

    /**
     * @var Format\Format
     */
    private $format;

    private function __construct(string $encoded, $decoded)
    {
        $this->encoded = $encoded;
        $this->decoded = $decoded;
    }

    /**
     * Returns the original JSON value.
     */
    public function __toString(): string
    {
        return $this->encoded;
    }

    /**
     * @throws Exception\InvalidJsonEncodedException
     */
    public static function fromEncoded(string $encoded): self
    {
        $decoded = \json_decode($encoded);

        if (null === $decoded && \JSON_ERROR_NONE !== \json_last_error()) {
            throw Exception\InvalidJsonEncodedException::fromEncoded($encoded);
        }

        return new self(
            $encoded,
            $decoded
        );
    }

    /**
     * Returns the decoded JSON value.
     *
     * @return null|array<mixed>|bool|float|int|\stdClass|string
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
     * Returns the format of the original JSON value.
     */
    public function format(): Format\Format
    {
        if (null === $this->format) {
            $this->format = Format\Format::fromJson($this);
        }

        return $this->format;
    }
}
