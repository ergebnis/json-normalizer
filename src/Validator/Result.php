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

namespace Ergebnis\Json\Normalizer\Validator;

final class Result
{
    /**
     * @phpstan-var list<string>
     * @psalm-var list<string>
     *
     * @var array<int, string>
     */
    private $errors;

    private function __construct(string ...$errors)
    {
        $this->errors = $errors;
    }

    public static function create(string ...$errors): self
    {
        return new self(...$errors);
    }

    public function isValid(): bool
    {
        return [] === $this->errors;
    }

    /**
     * @phpstan-return list<string>
     * @psalm-return list<string>
     *
     * @return array<int, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
