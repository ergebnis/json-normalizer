<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Normalizer\ChainNormalizer;
use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\Normalizer;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\ChainNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Json
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

        $results = \array_map(static function (int $step): Json {
            return Json::fromEncoded(
                <<<JSON
{
    "status": "normalized at step {$step}"
}
JSON
            );
        }, \range(0, 4));

        $last = \end($results);

        $normalizers = \array_map(function ($result) use ($json): Normalizer {
            static $previous = null;

            if (null === $previous) {
                $previous = $json;
            }

            $normalizer = $this->createMock(Normalizer::class);

            $normalizer
                ->expects(self::once())
                ->method('normalize')
                ->with(self::identicalTo($previous))
                ->willReturn($result);

            $previous = $result;

            return $normalizer;
        }, $results);

        $normalizer = new ChainNormalizer(...$normalizers);

        self::assertSame($last, $normalizer->normalize($json));
    }
}
