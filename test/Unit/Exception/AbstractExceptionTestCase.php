<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Exception;

use Ergebnis\Json\Normalizer\Test;
use PHPUnit\Framework;

/**
 * @internal
 */
abstract class AbstractExceptionTestCase extends Framework\TestCase
{
    use Test\Util\Helper;

    final protected function className(): string
    {
        $className = \preg_replace(
            '/Test$/',
            '',
            \str_replace(
                'Ergebnis\\Json\\Normalizer\\Test\\Unit\\Exception\\',
                'Ergebnis\\Json\\Normalizer\\Exception\\',
                static::class,
            ),
        );

        if (!\is_string($className)) {
            throw new \RuntimeException(\sprintf(
                'Unable to deduce source class name from test class name "%s".',
                static::class,
            ));
        }

        return $className;
    }
}
