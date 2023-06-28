<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\JsonEncodeNormalizer;
use Ergebnis\Json\Normalizer\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(JsonEncodeNormalizer::class)]
#[Framework\Attributes\UsesClass(Format\JsonEncodeOptions::class)]
final class JsonEncodeNormalizerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    #[Framework\Attributes\DataProvider('provideJsonEncodeOptions')]
    public function testNormalizeDecodesAndEncodesJsonWithJsonEncodeOptions(int $jsonEncodeOptions): void
    {
        $json = Json::fromString(
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

        $normalizer = new JsonEncodeNormalizer(Format\JsonEncodeOptions::fromInt($jsonEncodeOptions));

        $normalized = $normalizer->normalize($json);

        $expected = \json_encode(
            $json->decoded(),
            $jsonEncodeOptions,
        );

        self::assertJsonStringIdenticalToJsonString($expected, $normalized->encoded());
    }

    /**
     * @return \Generator<int, array{0: int}>
     */
    public static function provideJsonEncodeOptions(): \Generator
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
                0,
            );

            yield [
                $jsonEncodeOptions,
            ];
        }
    }

    /**
     * @see https://docstore.mik.ua/orelly/webprog/pcook/ch04_25.htm
     *
     * @param array<int, int> $elements
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
                    $combination,
                );
            }
        }

        return $combinations;
    }
}
