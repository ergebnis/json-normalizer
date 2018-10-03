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

namespace Localheinz\Json\Normalizer;

final class Json implements JsonInterface
{
    /**
     * @var string
     */
    private $encoded;

    /**
     * @var null|array|bool|float|int|\stdClass|string
     */
    private $decoded;

    /**
     * @var Format\FormatInterface
     */
    private $format;

    private function __construct(string $encoded, $decoded)
    {
        $this->encoded = $encoded;
        $this->decoded = $decoded;
    }

    public function __toString(): string
    {
        return $this->encoded;
    }

    /**
     * @param string $encoded
     *
     * @throws Exception\InvalidJsonEncodedException
     *
     * @return JsonInterface
     */
    public static function fromEncoded(string $encoded): JsonInterface
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

    public function decoded()
    {
        return $this->decoded;
    }

    public function encoded(): string
    {
        return $this->encoded;
    }

    public function format(): Format\FormatInterface
    {
        if (null === $this->format) {
            $this->format = Format\Format::fromJson($this);
        }

        return $this->format;
    }
}
