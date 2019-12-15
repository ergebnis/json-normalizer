<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Exception;

final class UriRetrieverRequiredException extends \InvalidArgumentException implements ExceptionInterface
{
    public static function create(): self
    {
        return new self('Cannot retrieve URIs when no retrievers have been injected.');
    }
}
