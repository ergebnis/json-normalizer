<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Exception;

final class UriRetrieverRequiredException extends \InvalidArgumentException implements ExceptionInterface
{
    public static function create(): self
    {
        return new self('Cannot retrieve URIs when no retrievers have been injected.');
    }
}
