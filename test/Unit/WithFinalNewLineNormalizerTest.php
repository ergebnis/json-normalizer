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

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\WithFinalNewLineNormalizer;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\WithFinalNewLineNormalizer
 */
final class WithFinalNewLineNormalizerTest extends Framework\TestCase
{
    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank()
     */
    public function testNormalizeEnsuresSingleFinalNewLine(string $whitespace): void
    {
        $json = Json::fromString(
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
