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

use Localheinz\Json\Normalizer\ChainNormalizer;
use Localheinz\Json\Normalizer\Json;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Prophecy\Argument;

/**
 * @internal
 * @coversNothing
 */
final class ChainNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizePassesJsonThroughNormalizers(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
    "status": "original"
}
JSON
        );

        $results = \array_map(static function (int $step) {
            return Json::fromEncoded(
                <<<JSON
{
    "status": "normalized at step {$step}"
}
JSON
            );
        }, \range(0, 4));

        $last = \end($results);

        $normalizers = \array_map(function ($result) use ($json): NormalizerInterface {
            static $previous = null;

            if (null === $previous) {
                $previous = $json;
            }

            $normalizer = $this->prophesize(NormalizerInterface::class);

            $normalizer
                ->normalize(Argument::is($previous))
                ->shouldBeCalled()
                ->willReturn($result);

            $previous = $result;

            return $normalizer->reveal();
        }, $results);

        $normalizer = new ChainNormalizer(...$normalizers);

        self::assertSame($last, $normalizer->normalize($json));
    }
}
