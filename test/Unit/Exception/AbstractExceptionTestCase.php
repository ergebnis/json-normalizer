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

use Localheinz\Json\Normalizer\Exception;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
abstract class AbstractExceptionTestCase extends Framework\TestCase
{
    use Helper;

    final public function testImplementsExceptionInterface(): void
    {
        self::assertClassImplementsInterface(Exception\ExceptionInterface::class, $this->className());
    }

    final protected function className(): string
    {
        $className = \preg_replace(
            '/Test$/',
            '',
            \str_replace(
                'Localheinz\\Json\\Normalizer\\Test\\Unit\\Exception\\',
                'Localheinz\\Json\\Normalizer\\Exception\\',
                static::class
            )
        );

        if (!\is_string($className)) {
            throw new \RuntimeException(\sprintf(
                'Unable to deduce source class name from test class name "%s".',
                static::class
            ));
        }

        return $className;
    }
}
