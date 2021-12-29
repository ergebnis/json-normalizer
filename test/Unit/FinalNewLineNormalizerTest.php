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

use Ergebnis\Json\Normalizer\FinalNewLineNormalizer;
use Ergebnis\Json\Normalizer\Json;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\FinalNewLineNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class FinalNewLineNormalizerTest extends AbstractNormalizerTestCase
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

        $normalizer = new FinalNewLineNormalizer();

        $normalized = $normalizer->normalize($json);

        $expected = \rtrim($json->encoded()) . \PHP_EOL;

        self::assertSame($expected, $normalized->encoded());
    }
}
