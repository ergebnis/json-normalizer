<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit;

use Localheinz\Json\Normalizer\Format\JsonEncodeOptions;
use Localheinz\Json\Normalizer\Json;
use Localheinz\Json\Normalizer\JsonEncodeNormalizer;

/**
 * @internal
 */
final class JsonEncodeNormalizerTest extends AbstractNormalizerTestCase
{
    /**
     * @dataProvider providerJsonEncodeOptions
     *
     * @param int $jsonEncodeOptions
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

    public function providerJsonEncodeOptions(): \Generator
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

        $combinations = $this->combinations($jsonEncodeFlags);

        foreach ($combinations as $combination) {
            $jsonEncodeOptions = \array_reduce(
                $combination,
                static function (int $jsonEncodeFlag, int $jsonEncodeOptions) {
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
     * @param array $elements
     *
     * @return array
     */
    private function combinations(array $elements): array
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
