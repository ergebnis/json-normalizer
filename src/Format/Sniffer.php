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

final class Sniffer implements SnifferInterface
{
    public function sniff(string $json): FormatInterface
    {
        if (null === \json_decode($json) && \JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid JSON.',
                $json
            ));
        }

        return new Format(
            $this->jsonEncodeOptions($json),
            $this->indent($json),
            $this->newLine($json),
            $this->hasFinalNewLine($json)
        );
    }

    private function jsonEncodeOptions(string $json): int
    {
        $jsonEncodeOptions = 0;

        if (false === \strpos($json, '\/')) {
            $jsonEncodeOptions |= \JSON_UNESCAPED_SLASHES;
        }

        if (1 !== \preg_match('/(\\\\+)u([0-9a-f]{4})/i', $json)) {
            $jsonEncodeOptions |= \JSON_UNESCAPED_UNICODE;
        }

        return $jsonEncodeOptions;
    }

    private function indent(string $json): IndentInterface
    {
        if (1 === \preg_match('/^(?P<indent>( +|\t+)).*/m', $json, $match)) {
            return Indent::fromString($match['indent']);
        }

        return Indent::fromSizeAndStyle(
            4,
            'space'
        );
    }

    private function newLine(string $json): NewLineInterface
    {
        if (1 === \preg_match('/(?P<newLine>\r\n|\n|\r)/', $json, $match)) {
            return NewLine::fromString($match['newLine']);
        }

        return NewLine::fromString(\PHP_EOL);
    }

    private function hasFinalNewLine(string $json): bool
    {
        if (\rtrim($json, " \t") === \rtrim($json)) {
            return false;
        }

        return true;
    }
}
