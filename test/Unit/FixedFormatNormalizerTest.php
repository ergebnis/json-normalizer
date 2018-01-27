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

use Localheinz\Json\Normalizer\FixedFormatNormalizer;
use Localheinz\Json\Normalizer\Format;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Prophecy\Argument;

final class FixedFormatNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeNormalizesAndFormatsUsingFormat(): void
    {
        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $normalized = <<<'JSON'
{
    "name": "Andreas Möller (normalized)",
    "url": "https://localheinz.com"
}
JSON;

        $formatted = <<<'JSON'
{
    "name": "Andreas Möller (printed)",
    "url": "https://localheinz.com"
}
JSON;

        $composedNormalizer = $this->prophesize(NormalizerInterface::class);

        $composedNormalizer
            ->normalize(Argument::is($json))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $format = $this->prophesize(Format\FormatInterface::class);

        $formatter = $this->prophesize(Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($format->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($formatted);

        $normalizer = new FixedFormatNormalizer(
            $composedNormalizer->reveal(),
            $format->reveal(),
            $formatter->reveal()
        );

        $this->assertSame($formatted, $normalizer->normalize($json));
    }

    public function providerFinalNewLine(): \Generator
    {
        $values = [
            'without-final-new-line' => [
                false,
                '',
            ],
            'with-final-new-line' => [
                true,
                PHP_EOL,
            ],
        ];

        foreach ($values as $key => [$hasFinalNewLine, $suffix]) {
            yield $key => [
                $hasFinalNewLine,
                $suffix,
            ];
        }
    }
}
