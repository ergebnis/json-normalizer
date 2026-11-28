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

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Printer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\IndentNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Format\Indent
 */
final class IndentNormalizerTest extends Framework\TestCase
{
    use Normalizer\Test\Util\Helper;

    public function testNormalizeUsesPrinterToNormalizeJsonWithIndent(): void
    {
        $indent = Normalizer\Format\Indent::fromString('  ');

        $json = Json::fromString(<<<'JSON'
            {
                "status": "original"
            }
            JSON);

        $indented = <<<'JSON'
            {
                "name": "Andreas Möller (indented)",
                "url": "https://localheinz.com"
            }
            JSON;

        $printer = $this->createMock(Printer\PrinterInterface::class);

        $printer
            ->expects(self::once())
            ->method('print')
            ->with(
                self::identicalTo($json->encoded()),
                self::identicalTo($indent->toString()),
            )
            ->willReturn($indented);

        $normalizer = new Normalizer\IndentNormalizer(
            $indent,
            $printer,
        );

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringIdenticalToJsonString($indented, $normalized->encoded());
    }
}
