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

use Ergebnis\DataProvider;
use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Normalizer\WithoutFinalNewLineNormalizer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\WithoutFinalNewLineNormalizer
 */
final class WithoutFinalNewLineNormalizerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider \Ergebnis\DataProvider\StringProvider::blank
     */
    public function testNormalizeRemovesAllWhitespaceFromEndOfJson(string $whitespace): void
    {
        $json = Json::fromString(
            <<<JSON
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}{$whitespace}
JSON
        );

        $normalizer = new WithoutFinalNewLineNormalizer();

        $normalized = $normalizer->normalize($json);

        $expected = \rtrim($json->encoded());

        self::assertJsonStringIdenticalToJsonString($expected, $normalized->encoded());
    }
}
