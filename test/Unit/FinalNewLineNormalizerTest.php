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

use Localheinz\Json\Normalizer\FinalNewLineNormalizer;
use Localheinz\Json\Normalizer\Json;

/**
 * @internal
 *
 * @covers \Localheinz\Json\Normalizer\FinalNewLineNormalizer
 */
final class FinalNewLineNormalizerTest extends AbstractNormalizerTestCase
{
    /**
     * @dataProvider providerWhitespace
     *
     * @param string $whitespace
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
