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

namespace Ergebnis\Json\Normalizer\Test\Unit\Exception;

use Ergebnis\Json\Normalizer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Exception\DependencyMissing
 */
final class DependencyMissingTest extends Framework\TestCase
{
    use Normalizer\Test\Util\Helper;

    public function testForReturnsDependencyMissing(): void
    {
        $faker = self::faker();

        $className = self::class;
        $packageName = \sprintf(
            $faker->slug(),
            $faker->slug(),
        );

        $exception = Normalizer\Exception\DependencyMissing::for(
            $className,
            $packageName,
        );

        $message = \sprintf(
            <<<'TXT'
                To use "%s", the package "%s" is required.

                Run

                composer require "%s"

                to install it.
                TXT,
            $className,
            $packageName,
            $packageName,
        );

        self::assertSame($message, $exception->getMessage());
    }
}
