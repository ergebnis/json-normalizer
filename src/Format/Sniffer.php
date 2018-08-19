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

use Localheinz\Json\Normalizer\JsonInterface;

final class Sniffer implements SnifferInterface
{
    public function sniff(JsonInterface $json): FormatInterface
    {
        $encoded = $json->encoded();

        return new Format(
            $this->jsonEncodeOptions($encoded),
            $this->indent($encoded),
            $this->newLine($encoded),
            $this->hasFinalNewLine($encoded)
        );
    }

    private function jsonEncodeOptions(string $encoded): int
    {
        $jsonEncodeOptions = 0;

        if (false === \strpos($encoded, '\/')) {
            $jsonEncodeOptions |= \JSON_UNESCAPED_SLASHES;
        }

        if (1 !== \preg_match('/(\\\\+)u([0-9a-f]{4})/i', $encoded)) {
            $jsonEncodeOptions |= \JSON_UNESCAPED_UNICODE;
        }

        return $jsonEncodeOptions;
    }

    private function indent(string $encoded): IndentInterface
    {
        if (1 === \preg_match('/^(?P<indent>( +|\t+)).*/m', $encoded, $match)) {
            return Indent::fromString($match['indent']);
        }

        return Indent::fromSizeAndStyle(
            4,
            'space'
        );
    }

    private function newLine(string $encoded): NewLineInterface
    {
        if (1 === \preg_match('/(?P<newLine>\r\n|\n|\r)/', $encoded, $match)) {
            return NewLine::fromString($match['newLine']);
        }

        return NewLine::fromString(\PHP_EOL);
    }

    private function hasFinalNewLine(string $encoded): bool
    {
        if (\rtrim($encoded, " \t") === \rtrim($encoded)) {
            return false;
        }

        return true;
    }
}
