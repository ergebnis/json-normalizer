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

namespace Ergebnis\Json\Normalizer\Test\Unit\Vendor\Composer;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Normalizer\Vendor;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\ComposerJsonNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\ChainNormalizer
 * @uses \Ergebnis\Json\Normalizer\SchemaNormalizer
 * @uses \Ergebnis\Json\Normalizer\Vendor\Composer\BinNormalizer
 * @uses \Ergebnis\Json\Normalizer\Vendor\Composer\PackageHashNormalizer
 * @uses \Ergebnis\Json\Normalizer\Vendor\Composer\VersionConstraintNormalizer
 */
final class ComposerJsonNormalizerTest extends AbstractComposerTestCase
{
    /**
     * @dataProvider provideScenario
     */
    public function testNormalizeNormalizes(Test\Fixture\Vendor\Composer\ComposerJsonNormalizer\Scenario $scenario): void
    {
        $json = $scenario->original();

        $normalizer = new Vendor\Composer\ComposerJsonNormalizer(\sprintf(
            'file://%s',
            \realpath(__DIR__ . '/../../../Fixture/Vendor/Composer/schema.json'),
        ));

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringEqualsJsonStringNormalized($scenario->normalized()->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<string, array{0: Test\Fixture\Vendor\Composer\ComposerJsonNormalizer\Scenario}>
     */
    public static function provideScenario(): \Generator
    {
        $basePath = __DIR__ . '/../';

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../../../Fixture/Vendor/Composer/ComposerJsonNormalizer/NormalizeNormalizes'));

        foreach ($iterator as $fileInfo) {
            /** @var \SplFileInfo $fileInfo */
            if (!$fileInfo->isFile()) {
                continue;
            }

            if ('normalized.json' !== $fileInfo->getBasename()) {
                continue;
            }

            $normalizedFile = $fileInfo->getRealPath();

            $originalFile = \preg_replace(
                '/normalized\.json$/',
                'original.json',
                $normalizedFile,
            );

            if (!\is_string($originalFile)) {
                throw new \RuntimeException(\sprintf(
                    'Unable to deduce original JSON file name from normalized JSON file name "%s".',
                    $normalizedFile,
                ));
            }

            if (!\file_exists($originalFile)) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to exist, but it does not.',
                    $originalFile,
                ));
            }

            $key = \substr(
                $fileInfo->getPath(),
                \strlen($basePath),
            );

            yield $key => [
                Test\Fixture\Vendor\Composer\ComposerJsonNormalizer\Scenario::create(
                    $key,
                    Json::fromFile($originalFile),
                    Json::fromFile($normalizedFile),
                ),
            ];
        }
    }
}
