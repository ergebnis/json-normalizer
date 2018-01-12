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

use Localheinz\Json\Normalizer\NoFinalNewLineNormalizer;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

final class NoFinalNewLineNormalizerTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsNormalizerInterface()
    {
        $this->assertClassImplementsInterface(NormalizerInterface::class, NoFinalNewLineNormalizer::class);
    }

    public function testNormalizeRejectsInvalidJson()
    {
        $json = $this->faker()->realText();

        $normalizer = new NoFinalNewLineNormalizer();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid JSON.',
            $json
        ));

        $normalizer->normalize($json);
    }

    /**
     * @dataProvider providerWhitespace
     *
     * @param string $whitespace
     */
    public function testNormalizeRemovesAllWhitespaceFromEndOfJson(string $whitespace)
    {
        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;
        $json .= $whitespace;

        $normalized = \rtrim($json);

        $normalizer = new NoFinalNewLineNormalizer();

        $this->assertSame($normalized, $normalizer->normalize($json));
    }

    public function providerWhitespace()
    {
        $values = [
            '',
            ' ',
            "\t",
            PHP_EOL,
        ];

        foreach ($values as $value) {
            yield [
                $value,
            ];
        }
    }
}
