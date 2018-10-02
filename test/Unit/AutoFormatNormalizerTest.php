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

use Localheinz\Json\Format\FormatInterface;
use Localheinz\Json\JsonInterface;
use Localheinz\Json\Normalizer\AutoFormatNormalizer;
use Localheinz\Json\Normalizer\Format;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Prophecy\Argument;

/**
 * @internal
 */
final class AutoFormatNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeNormalizesAndFormatsUsingJsonFormat(): void
    {
        $format = $this->prophesize(FormatInterface::class);

        $json = $this->prophesize(JsonInterface::class);

        $json
            ->format()
            ->shouldBeCalled()
            ->willReturn($format->reveal());

        $normalized = $this->prophesize(JsonInterface::class);
        $formatted = $this->prophesize(JsonInterface::class);

        $composedNormalizer = $this->prophesize(NormalizerInterface::class);

        $composedNormalizer
            ->normalize(Argument::is($json->reveal()))
            ->shouldBeCalled()
            ->willReturn($normalized->reveal());

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
            $formatter->reveal()
        );

        $this->assertSame($formatted->reveal(), $normalizer->normalize($json->reveal()));
    }
}
