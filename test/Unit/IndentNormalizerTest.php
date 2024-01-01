<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\IndentNormalizer;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Printer\PrinterInterface;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(IndentNormalizer::class)]
#[Framework\Attributes\UsesClass(Format\Indent::class)]
final class IndentNormalizerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testNormalizeUsesPrinterToNormalizeJsonWithIndent(): void
    {
        $indent = Format\Indent::fromString('  ');

        $json = Json::fromString(
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

        $printer = $this->createMock(PrinterInterface::class);

        $printer
            ->expects(self::once())
            ->method('print')
            ->with(
                self::identicalTo($json->encoded()),
                self::identicalTo($indent->toString()),
            )
            ->willReturn($indented);

        $normalizer = new IndentNormalizer(
            $indent,
            $printer,
        );

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringIdenticalToJsonString($indented, $normalized->encoded());
    }
}
