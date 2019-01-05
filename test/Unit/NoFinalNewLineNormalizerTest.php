<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit;

use Localheinz\Json\Normalizer\Json;
use Localheinz\Json\Normalizer\NoFinalNewLineNormalizer;

/**
 * @internal
 */
final class NoFinalNewLineNormalizerTest extends AbstractNormalizerTestCase
{
    /**
     * @dataProvider providerWhitespace
     *
     * @param string $whitespace
     */
    public function testNormalizeRemovesAllWhitespaceFromEndOfJson(string $whitespace): void
    {
        $json = Json::fromEncoded(
<<<JSON
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}${whitespace}
JSON
        );

        $normalizer = new NoFinalNewLineNormalizer();

        $normalized = $normalizer->normalize($json);

        $expected = \rtrim($json->encoded());

        self::assertSame($expected, $normalized->encoded());
    }

    public function providerWhitespace(): \Generator
    {
        $values = [
            '',
            ' ',
            "\t",
            \PHP_EOL,
        ];

        foreach ($values as $value) {
            yield [
                $value,
            ];
        }
    }
}
