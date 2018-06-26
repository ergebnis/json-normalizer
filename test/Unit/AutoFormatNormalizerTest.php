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

use Localheinz\Json\Normalizer\AutoFormatNormalizer;
use Localheinz\Json\Normalizer\Format;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Prophecy\Argument;

/**
 * @internal
 */
final class AutoFormatNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeUsesSnifferToSniffFormatNormalizesAndFormatsUsingSniffedFormat(): void
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
    "name": "Andreas Möller (formatted)",
    "url": "https://localheinz.com"
}
JSON;

        $composedNormalizer = $this->prophesize(NormalizerInterface::class);

        $composedNormalizer
            ->normalize(Argument::is($json))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $format = $this->prophesize(Format\FormatInterface::class);

        $sniffer = $this->prophesize(Format\SnifferInterface::class);

        $sniffer
            ->sniff(Argument::is($json))
            ->shouldBeCalled()
            ->willReturn($format);

        $formatter = $this->prophesize(Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($format->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($formatted);

        $normalizer = new AutoFormatNormalizer(
            $composedNormalizer->reveal(),
            $sniffer->reveal(),
            $formatter->reveal()
        );

        $this->assertSame($formatted, $normalizer->normalize($json));
    }
}
