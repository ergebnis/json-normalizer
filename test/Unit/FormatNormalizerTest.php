<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
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
use PHPUnit\Framework;

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
final class FormatNormalizerTest extends Framework\TestCase
{
    /**
     * @dataProvider provideScenario
     */
    public function testNormalizeNormalizes(Test\Fixture\FormatNormalizer\NormalizeNormalizesJson\Scenario $scenario): void
    {
        $normalizer = new FormatNormalizer(
            new Printer\Printer(),
            $scenario->format(),
        );

        $normalized = $normalizer->normalize($scenario->original());

        self::assertSame($scenario->normalized()->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<string, array{0: Test\Fixture\FormatNormalizer\NormalizeNormalizesJson\Scenario}>
     */
    public function provideScenario(): \Generator
    {
        $basePath = __DIR__ . '/../';

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../Fixture/FormatNormalizer/NormalizeNormalizesJson'));

        foreach ($iterator as $fileInfo) {
            /** @var \SplFileInfo $fileInfo */
            if (!$fileInfo->isFile()) {
                continue;
            }

            if ('original.json' !== $fileInfo->getBasename()) {
                continue;
            }

            $originalFile = $fileInfo->getRealPath();

            $normalizedFile = \preg_replace(
                '/original\.json$/',
                'normalized.json',
                $originalFile,
            );

            if (!\is_string($normalizedFile)) {
                throw new \RuntimeException(\sprintf(
                    'Unable to deduce normalized JSON file name from original JSON file name "%s".',
                    $originalFile,
                ));
            }

            if (!\file_exists($normalizedFile)) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to exist, but it does not.',
                    $normalizedFile,
                ));
            }

            $formatFile = \preg_replace(
                '/original\.json$/',
                'format.php',
                $originalFile,
            );

            if (!\is_string($formatFile)) {
                throw new \RuntimeException(\sprintf(
                    'Unable to deduce format file name from original JSON file name "%s".',
                    $originalFile,
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
                Test\Fixture\FormatNormalizer\NormalizeNormalizesJson\Scenario::create(
                    $key,
                    $format,
                    Json::fromFile($originalFile),
                    Json::fromFile($normalizedFile),
                ),
            ];
        }
    }
}
