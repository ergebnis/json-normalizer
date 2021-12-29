<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2021 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Vendor\Composer;

use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\Vendor;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\BinNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Json
 */
final class BinNormalizerTest extends AbstractComposerTestCase
{
    public function testNormalizeDoesNotModifyOtherProperty(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\BinNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($json->encoded(), $normalized->encoded());
    }

    public function testNormalizeDoesNotModifyBinIfPropertyExistsAsString(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "bin": "foo.php",
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\BinNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($json->encoded(), $normalized->encoded());
    }

    public function testNormalizeSortsBinIfPropertyExistsAsArray(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
  "bin": [
    "script.php",
    "another-script.php"
  ],
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON
        );

        $expected = Json::fromEncoded(
            <<<'JSON'
{
  "bin": [
    "another-script.php",
    "script.php"
  ],
  "foo": {
    "qux": "quux",
    "bar": "baz"
  }
}
JSON
        );

        $normalizer = new Vendor\Composer\BinNormalizer();

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($expected->encoded(), $normalized->encoded());
    }
}
