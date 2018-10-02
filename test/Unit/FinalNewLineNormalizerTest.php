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

use Localheinz\Json\JsonInterface;
use Localheinz\Json\Normalizer\FinalNewLineNormalizer;

/**
 * @internal
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
        $encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;
        $encoded .= $whitespace;

        $json = $this->prophesize(JsonInterface::class);

        $json
            ->encoded()
            ->shouldBeCalled()
            ->willReturn($encoded);

        $normalizer = new FinalNewLineNormalizer();

        $normalized = $normalizer->normalize($json->reveal());

        $this->assertSame(\rtrim($encoded) . \PHP_EOL, $normalized->encoded());
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
