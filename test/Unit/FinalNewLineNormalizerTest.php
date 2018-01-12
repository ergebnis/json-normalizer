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

use Localheinz\Json\Normalizer\FinalNewLineNormalizer;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

final class FinalNewLineNormalizerTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsNormalizerInterface()
    {
        $this->assertClassImplementsInterface(NormalizerInterface::class, FinalNewLineNormalizer::class);
    }

    public function testNormalizeRejectsInvalidJson()
    {
        $json = $this->faker()->realText();

        $normalizer = new FinalNewLineNormalizer();

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
    public function testNormalizeEnsuresSingleFinalNewLine(string $whitespace)
    {
        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $normalized = \rtrim($json . $whitespace) . PHP_EOL;

        $normalizer = new FinalNewLineNormalizer();

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
