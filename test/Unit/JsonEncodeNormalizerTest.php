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

use Ergebnis\Json\Normalizer\Format\JsonEncodeOptions;
use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\JsonEncodeNormalizer;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\JsonEncodeNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class JsonEncodeNormalizerTest extends AbstractNormalizerTestCase
{
    /**
     * @dataProvider provideJsonEncodeOptions
     */
    public function testNormalizeDecodesAndEncodesJsonWithJsonEncodeOptions(int $jsonEncodeOptions): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
    "name": "Andreas M\u00f6ller",
    "url": "https:\/\/github.com\/localheinz\/json-normalizer",
    "string-apostroph": "'",
    "string-numeric": "9000",
    "string-quote": "\"",
    "string-tag": "<p>"
}
JSON
        );

        $normalizer = new JsonEncodeNormalizer(JsonEncodeOptions::fromInt($jsonEncodeOptions));

        $normalized = $normalizer->normalize($json);

        $expected = \json_encode(
            $json->decoded(),
            $jsonEncodeOptions
        );

        self::assertSame($expected, $normalized->encoded());
    }

    /**
     * @return \Generator<array<int>>
     */
    public function provideJsonEncodeOptions(): \Generator
    {
        /**
         * Could add more, but the idea counts.
         */
        $jsonEncodeFlags = [
            \JSON_HEX_APOS,
            \JSON_HEX_QUOT,
            \JSON_HEX_TAG,
            \JSON_NUMERIC_CHECK,
            \JSON_UNESCAPED_SLASHES,
            \JSON_UNESCAPED_UNICODE,
        ];

        $combinations = self::combinations($jsonEncodeFlags);

        foreach ($combinations as $combination) {
            $jsonEncodeOptions = \array_reduce(
                $combination,
                static function (int $jsonEncodeFlag, int $jsonEncodeOptions): int {
                    $jsonEncodeOptions |= $jsonEncodeFlag;

                    return $jsonEncodeOptions;
                },
                0
            );

            yield [
                $jsonEncodeOptions,
            ];
        }
    }

    /**
     * @see https://docstore.mik.ua/orelly/webprog/pcook/ch04_25.htm
     *
     * @param int[] $elements
     *
     * @return array<array<int>>
     */
    private static function combinations(array $elements): array
    {
        $combinations = [[]];

        foreach ($elements as $element) {
            foreach ($combinations as $combination) {
                $combinations[] = \array_merge(
                    [
                        $element,
                    ],
                    $combination
                );
            }
        }

        return $combinations;
    }
}
