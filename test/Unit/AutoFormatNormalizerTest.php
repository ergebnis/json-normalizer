<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit;

use Localheinz\Json\Normalizer\AutoFormatNormalizer;
use Localheinz\Json\Normalizer\Format;
use Localheinz\Json\Normalizer\Json;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Prophecy\Argument;

/**
 * @internal
 * @coversNothing
 */
final class AutoFormatNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeNormalizesAndFormatsUsingJsonFormat(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
    "status": "original"
}
JSON
        );

        $normalized = Json::fromEncoded(
            <<<'JSON'
{
    "status": "normalized"
}
JSON
        );

        $formatted = Json::fromEncoded(
            <<<'JSON'
{
    "status": "formatted"
}
JSON
        );

        $composedNormalizer = $this->prophesize(NormalizerInterface::class);

        $composedNormalizer
            ->normalize(Argument::is($json))
            ->shouldBeCalled()
            ->willReturn($normalized);

        $formatter = $this->prophesize(Format\FormatterInterface::class);

        $formatter
            ->format(
                Argument::is($normalized),
                Argument::is($json->format())
            )
            ->shouldBeCalled()
            ->willReturn($formatted);

        $normalizer = new AutoFormatNormalizer(
            $composedNormalizer->reveal(),
            $formatter->reveal()
        );

        self::assertSame($formatted, $normalizer->normalize($json));
    }
}
