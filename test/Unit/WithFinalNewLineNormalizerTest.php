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

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\WithFinalNewLineNormalizer;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\WithFinalNewLineNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class WithFinalNewLineNormalizerTest extends AbstractNormalizerTestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     */
    public function testNormalizeEnsuresSingleFinalNewLine(string $whitespace): void
    {
        $json = Json::fromEncoded(
            <<<JSON
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}{$whitespace}
JSON
        );

        $normalizer = new WithFinalNewLineNormalizer();

        $normalized = $normalizer->normalize($json);

        $expected = \rtrim($json->encoded()) . \PHP_EOL;

        self::assertSame($expected, $normalized->encoded());
    }
}
