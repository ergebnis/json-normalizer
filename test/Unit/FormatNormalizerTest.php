<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
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
 * @covers \Ergebnis\Json\Normalizer\FormatNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Format\Format
 * @uses \Ergebnis\Json\Normalizer\Format\Indent
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\Format\NewLine
 */
final class FormatNormalizerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider provideScenario
     */
    public function testNormalizeNormalizes(Test\Fixture\FormatNormalizer\NormalizeNormalizesJson\Scenario $scenario): void
    {
        $normalizer = new FormatNormalizer(
            new Printer\Printer(),
            $scenario->format(),
        );

        $normalized = $normalizer->normalize($scenario->input());

        self::assertJsonStringIdenticalToJsonString($scenario->output()->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<string, array{0: Test\Fixture\FormatNormalizer\NormalizeNormalizesJson\Scenario}>
     */
    public static function provideScenario(): iterable
    {
        $basePath = __DIR__ . '/../';

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../Fixture/FormatNormalizer/NormalizeNormalizesJson'));

        foreach ($iterator as $fileInfo) {
            /** @var \SplFileInfo $fileInfo */
            if (!$fileInfo->isFile()) {
                continue;
            }

            if ('input.json' !== $fileInfo->getBasename()) {
                continue;
            }

            $inputFile = $fileInfo->getRealPath();

            $outputFile = \preg_replace(
                '/input\.json$/',
                'output.json',
                $inputFile,
            );

            if (!\is_string($outputFile)) {
                throw new \RuntimeException(\sprintf(
                    'Unable to deduce output JSON file name from input JSON file name "%s".',
                    $inputFile,
                ));
            }

            if (!\file_exists($outputFile)) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to exist, but it does not.',
                    $outputFile,
                ));
            }

            $formatFile = \preg_replace(
                '/input\.json$/',
                'format.php',
                $inputFile,
            );

            if (!\is_string($formatFile)) {
                throw new \RuntimeException(\sprintf(
                    'Unable to deduce format file name from input JSON file name "%s".',
                    $inputFile,
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
                    Json::fromFile($inputFile),
                    Json::fromFile($outputFile),
                ),
            ];
        }
    }
}
