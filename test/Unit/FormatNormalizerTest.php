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

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Format;
use Ergebnis\Json\Normalizer\FormatNormalizer;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Printer;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\FormatNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Format\Format
 * @uses \Ergebnis\Json\Normalizer\Format\Indent
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\Format\NewLine
 */
final class FormatNormalizerTest extends AbstractNormalizerTestCase
{
    /**
     * @dataProvider provideScenario
     */
    public function testNormalizeNormalizes(Test\Fixture\FormatNormalizer\Scenario $scenario): void
    {
        $normalizer = new FormatNormalizer(
            new Printer\Printer(),
            $scenario->format(),
        );

        $normalized = $normalizer->normalize($scenario->original());

        self::assertSame($scenario->normalized()->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<string, array{0: Test\Fixture\FormatNormalizer\Scenario}>
     */
    public function provideScenario(): \Generator
    {
        $basePath = __DIR__ . '/../';

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../Fixture/FormatNormalizer/NormalizeNormalizes'));

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

            $formatFile = \preg_replace(
                '/normalized\.json$/',
                'format.php',
                $normalizedFile,
            );

            if (!\is_string($formatFile)) {
                throw new \RuntimeException(\sprintf(
                    'Unable to deduce format  file name from normalized JSON file name "%s".',
                    $normalizedFile,
                ));
            }

            if (!\file_exists($formatFile)) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to exist, but it does not.',
                    $formatFile,
                ));
            }

            $format = include $formatFile;

            if (!$format instanceof Format\Format) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to return an instance of "%s", got "%s" instead.',
                    $formatFile,
                    Format\Format::class,
                    \get_debug_type($format),
                ));
            }

            $key = \substr(
                $fileInfo->getPath(),
                \strlen($basePath),
            );

            yield $key => [
                Test\Fixture\FormatNormalizer\Scenario::create(
                    $key,
                    $format,
                    Json::fromFile($originalFile),
                    Json::fromFile($normalizedFile),
                ),
            ];
        }
    }
}
