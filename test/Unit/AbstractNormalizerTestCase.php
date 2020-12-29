<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Normalizer\NormalizerInterface;
use Ergebnis\Json\Printer;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
abstract class AbstractNormalizerTestCase extends Framework\TestCase
{
    use Helper;

    final public function testImplementsNormalizerInterface(): void
    {
        self::assertClassImplementsInterface(NormalizerInterface::class, $this->className());
    }

    final protected function className(): string
    {
        $className = \preg_replace(
            '/Test$/',
            '',
            \str_replace(
                'Ergebnis\\Json\\Normalizer\\Test\\Unit\\',
                'Ergebnis\\Json\\Normalizer\\',
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

    final protected static function assertJsonStringEqualsJsonStringNormalized(string $expected, string $actual): void
    {
        $printer = new Printer\Printer();

        $normalize = static function (string $json) use ($printer): string {
            self::assertJson($json);

            $normalized = \json_encode(\json_decode($json));

            self::assertIsString($normalized);

            return $printer->print($normalized);
        };

        self::assertSame($normalize($expected), $normalize($actual));
    }
}
