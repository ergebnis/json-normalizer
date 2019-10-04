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

namespace Localheinz\Json\Normalizer\Test\Unit\Exception;

use Localheinz\Json\Normalizer\Exception\InvalidNewLineStringException;
use Localheinz\Json\Normalizer\Exception\UriRetrieverRequiredException;

/**
 * @internal
 * @coversNothing
 */
final class UriRetrieverRequiredExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsInvalidArgumentException(): void
    {
        $this->assertClassExtends(\InvalidArgumentException::class, InvalidNewLineStringException::class);
    }

    public function testCreateReturnsUriRetrieverRequiredException(): void
    {
        $exception = UriRetrieverRequiredException::create();

        self::assertInstanceOf(UriRetrieverRequiredException::class, $exception);
        self::assertSame('Cannot retrieve URIs when no retrievers have been injected.', $exception->getMessage());
    }
}
