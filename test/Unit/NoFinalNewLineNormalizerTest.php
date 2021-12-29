<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\NoFinalNewLineNormalizer;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\NoFinalNewLineNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Format\Format
 * @uses \Ergebnis\Json\Normalizer\Format\Indent
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\Format\NewLine
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class NoFinalNewLineNormalizerTest extends AbstractNormalizerTestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     */
    public function testNormalizeRemovesAllWhitespaceFromEndOfJson(string $whitespace): void
    {
        $json = Json::fromEncoded(
            <<<JSON
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}{$whitespace}
JSON
        );

        $normalizer = new NoFinalNewLineNormalizer();

        $normalized = $normalizer->normalize($json);

        $expected = \rtrim($json->encoded());

        self::assertSame($expected, $normalized->encoded());
    }
}
