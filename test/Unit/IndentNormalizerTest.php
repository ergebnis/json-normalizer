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

use Localheinz\Json\Format\IndentInterface;
use Localheinz\Json\JsonInterface;
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
        $indentString = $this->faker()->randomElement([
            ' ',
            "\t",
        ]);

        $indent = $this->prophesize(IndentInterface::class);

        $indent
            ->__toString()
            ->shouldBeCalled()
            ->willReturn($indentString);

        $encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $json = $this->prophesize(JsonInterface::class);

        $json
            ->encoded()
            ->shouldBeCalled()
            ->willReturn($encoded);

        $indented = <<<'JSON'
{
    "name": "Andreas Möller (indented)",
    "url": "https://localheinz.com"
}
JSON;

        $printer = $this->prophesize(PrinterInterface::class);

        $printer
            ->print(
                Argument::is($encoded),
                Argument::is($indentString)
            )
            ->shouldBeCalled()
            ->willReturn($indented);

        $normalizer = new IndentNormalizer(
            $indent->reveal(),
            $printer->reveal()
        );

        $normalized = $normalizer->normalize($json->reveal());

        $this->assertSame($indented, $normalized->encoded());
    }
}
