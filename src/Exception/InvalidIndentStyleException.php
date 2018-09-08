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

final class InvalidIndentStyleException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @var string
     */
    private $style;

    /**
     * @var string[]
     */
    private $allowedStyles;

    public static function fromStyleAndAllowedStyles(string $style, string ...$allowedStyles): self
    {
        $exception = new self(\sprintf(
            'Style needs to be one of "%s", but "%s" is not.',
            \implode('", "', $allowedStyles),
            $style
        ));

        $exception->style = $style;
        $exception->allowedStyles = $allowedStyles;

        return $exception;
    }

    public function style(): string
    {
        return $this->style;
    }

    /**
     * @return string[]
     */
    public function allowedStyles(): array
    {
        return $this->allowedStyles;
    }
}
