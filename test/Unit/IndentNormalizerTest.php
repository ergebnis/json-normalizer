<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit;

use Localheinz\Json\Normalizer\Format\IndentInterface;
use Localheinz\Json\Normalizer\IndentNormalizer;
use Localheinz\Json\Printer\PrinterInterface;
use Prophecy\Argument;

/**
 * @internal
 */
final class IndentNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeUsesPrinterToNormalizeJsonWithIndent(): void
    {
        $string = $this->faker()->randomElement([
            ' ',
            "\t",
        ]);

        $indent = $this->prophesize(IndentInterface::class);

        $indent
            ->__toString()
            ->shouldBeCalled()
            ->willReturn($string);

        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $normalized = <<<'JSON'
{
    "name": "Andreas Möller (normalized)",
    "url": "https://localheinz.com"
}
JSON;

        $printer = $this->prophesize(PrinterInterface::class);

        $printer
            ->print(
                Argument::is($json),
                Argument::is($string)
            )
            ->shouldBeCalled()
            ->willReturn($normalized);

        $normalizer = new IndentNormalizer(
            $indent->reveal(),
            $printer->reveal()
        );

        $this->assertSame($normalized, $normalizer->normalize($json));
    }
}
