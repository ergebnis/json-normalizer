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

namespace Ergebnis\Json\Normalizer\Test\Unit\Exception;

use Ergebnis\Json\Normalizer\Exception\UriRetrieverRequiredException;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\UriRetrieverRequiredException
 */
final class UriRetrieverRequiredExceptionTest extends AbstractExceptionTestCase
{
    public function testCreateReturnsUriRetrieverRequiredException(): void
    {
        $exception = UriRetrieverRequiredException::create();

        self::assertSame('Cannot retrieve URIs when no retrievers have been injected.', $exception->getMessage());
    }
}
