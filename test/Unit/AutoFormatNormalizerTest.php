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
use Localheinz\Json\Normalizer\JsonInterface;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Prophecy\Argument;

/**
 * @internal
 */
final class AutoFormatNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeUsesSnifferToSniffFormatNormalizesAndFormatsUsingSniffedFormat(): void
    {
        $json = $this->prophesize(JsonInterface::class);
        $normalized = $this->prophesize(JsonInterface::class);
        $formatted = $this->prophesize(JsonInterface::class);

        $composedNormalizer = $this->prophesize(NormalizerInterface::class);

        $composedNormalizer
            ->normalize(Argument::is($json->reveal()))
            ->shouldBeCalled()
            ->willReturn($normalized->reveal());

        $format = $this->prophesize(Format\FormatInterface::class);

        $sniffer = $this->prophesize(Format\SnifferInterface::class);

        $sniffer
            ->sniff(Argument::is($json->reveal()))
            ->shouldBeCalled()
            ->willReturn($format->reveal());

        $formatter = $this->prophesize(Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized->reveal()),
                Argument::is($format->reveal())
            )
            ->shouldBeCalled()
            ->willReturn($formatted->reveal());

        $normalizer = new AutoFormatNormalizer(
            $composedNormalizer->reveal(),
            $sniffer->reveal(),
            $formatter->reveal()
        );

        $this->assertSame($formatted->reveal(), $normalizer->normalize($json->reveal()));
    }
}
