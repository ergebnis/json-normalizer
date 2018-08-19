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
use Localheinz\Json\Normalizer\JsonInterface;

final class Format implements FormatInterface
{
    /**
     * @var int
     */
    private $jsonEncodeOptions;

    /**
     * @var IndentInterface
     */
    private $indent;

    /**
     * @var NewLineInterface
     */
    private $newLine;

    /**
     * @var bool
     */
    private $hasFinalNewLine;

    /**
     * @param int              $jsonEncodeOptions
     * @param IndentInterface  $indent
     * @param NewLineInterface $newLine
     * @param bool             $hasFinalNewLine
     *
     * @throws Exception\InvalidJsonEncodeOptionsException
     */
    public function __construct(int $jsonEncodeOptions, IndentInterface $indent, NewLineInterface $newLine, bool $hasFinalNewLine)
    {
        if (0 > $jsonEncodeOptions) {
            throw Exception\InvalidJsonEncodeOptionsException::fromJsonEncodeOptions($jsonEncodeOptions);
        }

        $this->jsonEncodeOptions = $jsonEncodeOptions;
        $this->indent = $indent;
        $this->newLine = $newLine;
        $this->hasFinalNewLine = $hasFinalNewLine;
    }

    public static function fromJson(JsonInterface $json): FormatInterface
    {
        $encoded = $json->encoded();

        return new self(
            self::detectJsonEncodeOptions($encoded),
            self::detectIndent($encoded),
            self::detectNewLine($encoded),
            self::detectHasFinalNewLine($encoded)
        );
    }

    public function jsonEncodeOptions(): int
    {
        return $this->jsonEncodeOptions;
    }

    public function indent(): IndentInterface
    {
        return $this->indent;
    }

    public function newLine(): NewLineInterface
    {
        return $this->newLine;
    }

    public function hasFinalNewLine(): bool
    {
        return $this->hasFinalNewLine;
    }

    public function withJsonEncodeOptions(int $jsonEncodeOptions): FormatInterface
    {
        if (0 > $jsonEncodeOptions) {
            throw Exception\InvalidJsonEncodeOptionsException::fromJsonEncodeOptions($jsonEncodeOptions);
        }

        $mutated = clone $this;

        $mutated->jsonEncodeOptions = $jsonEncodeOptions;

        return $mutated;
    }

    public function withIndent(IndentInterface $indent): FormatInterface
    {
        $mutated = clone $this;

        $mutated->indent = $indent;

        return $mutated;
    }

    public function withNewLine(NewLineInterface $newLine): FormatInterface
    {
        $mutated = clone $this;

        $mutated->newLine = $newLine;

        return $mutated;
    }

    public function withHasFinalNewLine(bool $hasFinalNewLine): FormatInterface
    {
        $mutated = clone $this;

        $mutated->hasFinalNewLine = $hasFinalNewLine;

        return $mutated;
    }

    private static function detectJsonEncodeOptions(string $encoded): int
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

    private static function detectIndent(string $encoded): IndentInterface
    {
        if (1 === \preg_match('/^(?P<indent>( +|\t+)).*/m', $encoded, $match)) {
            return Indent::fromString($match['indent']);
        }

        return Indent::fromSizeAndStyle(
            4,
            'space'
        );
    }

    private static function detectNewLine(string $encoded): NewLineInterface
    {
        if (1 === \preg_match('/(?P<newLine>\r\n|\n|\r)/', $encoded, $match)) {
            return NewLine::fromString($match['newLine']);
        }

        return NewLine::fromString(\PHP_EOL);
    }

    private static function detectHasFinalNewLine(string $encoded): bool
    {
        if (\rtrim($encoded, " \t") === \rtrim($encoded)) {
            return false;
        }

        return true;
    }
}
