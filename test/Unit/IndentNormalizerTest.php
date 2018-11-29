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

use Localheinz\Json\Normalizer\Format\Indent;
use Localheinz\Json\Normalizer\IndentNormalizer;
use Localheinz\Json\Normalizer\Json;
use Localheinz\Json\Printer\PrinterInterface;
use Prophecy\Argument;

/**
 * @internal
 */
final class IndentNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeUsesPrinterToNormalizeJsonWithIndent(): void
    {
        $indent = Indent::fromString('  ');

        $json = Json::fromEncoded(
<<<'JSON'
{
    "status": "original"
}
JSON
        );

        $indented = <<<'JSON'
{
    "name": "Andreas Möller (indented)",
    "url": "https://localheinz.com"
}
JSON;

        $printer = $this->prophesize(PrinterInterface::class);

        $printer
            ->print(
                Argument::is($json->encoded()),
                Argument::is($indent->__toString())
            )
            ->shouldBeCalled()
            ->willReturn($indented);

        $normalizer = new IndentNormalizer(
            $indent,
            $printer->reveal()
        );

        $normalized = $normalizer->normalize($json);

        self::assertSame($indented, $normalized->encoded());
    }
}
