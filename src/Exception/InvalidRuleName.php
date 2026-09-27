<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Exception;

final class InvalidRuleName extends \InvalidArgumentException implements Exception
{
    private string $value = '';

    public static function notKebabCaseSegments(string $value): self
    {
        $exception = new self(\sprintf(
            'Rule name "%s" must consist of kebab-case segments separated by slashes.',
            $value,
        ));

        $exception->value = $value;

        return $exception;
    }

    public function value(): string
    {
        return $this->value;
    }
}
