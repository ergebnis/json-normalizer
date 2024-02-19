<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Vendor\Composer;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Normalizer\Vendor;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\SchemaNormalizer
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\BinNormalizer
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\ComposerJsonNormalizer
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\ConfigHashNormalizer
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\PackageHashNormalizer
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\RepositoriesHashNormalizer
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\VersionConstraintNormalizer
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\WildcardSorter
 *
 * @uses \Ergebnis\Json\Normalizer\ChainNormalizer
 * @uses \Ergebnis\Json\Normalizer\Exception\OriginalInvalidAccordingToSchema
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\SchemaNormalizer
 * @uses \Ergebnis\Json\Normalizer\WithFinalNewLineNormalizer
 */
final class ComposerJsonNormalizerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider provideScenarioWhereJsonIsInvalidAccordingToSchema
     */
    public function testNormalizeRejectsJsonWhenItIsInvalidAccordingToSchema(Test\Fixture\Vendor\Composer\ComposerJsonNormalizer\NormalizeRejectsJson\Scenario $scenario): void
    {
        $json = $scenario->original();

        $normalizer = new Vendor\Composer\ComposerJsonNormalizer(\sprintf(
            'file://%s',
            \realpath(__DIR__ . '/../../../Fixture/Vendor/Composer/schema.json'),
        ));

        $this->expectException(Exception\OriginalInvalidAccordingToSchema::class);

        $normalizer->normalize($json);
    }

    /**
     * @return \Generator<string, array{0: Test\Fixture\Vendor\Composer\ComposerJsonNormalizer\NormalizeRejectsJson\Scenario}>
     */
    public static function provideScenarioWhereJsonIsInvalidAccordingToSchema(): iterable
    {
        $basePath = __DIR__ . '/../../../';

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../../../Fixture/Vendor/Composer/ComposerJsonNormalizer/NormalizeRejectsJson'));

        foreach ($iterator as $fileInfo) {
            /** @var \SplFileInfo $fileInfo */
            if (!$fileInfo->isFile()) {
                continue;
            }

            if ('original.json' !== $fileInfo->getBasename()) {
                continue;
            }

            $originalFile = $fileInfo->getRealPath();

            $key = \substr(
                $fileInfo->getPath(),
                \strlen($basePath),
            );

            yield $key => [
                Test\Fixture\Vendor\Composer\ComposerJsonNormalizer\NormalizeRejectsJson\Scenario::create(
                    $key,
                    Json::fromFile($originalFile),
                ),
            ];
        }
    }

    /**
     * @dataProvider provideScenarioWhereJsonIsValidAccordingToSchema
     */
    public function testNormalizeNormalizesJsonWhenItIsValidAccordingToSchema(Test\Fixture\Vendor\Composer\ComposerJsonNormalizer\NormalizeNormalizesJson\Scenario $scenario): void
    {
        $json = $scenario->original();

        $normalizer = new Vendor\Composer\ComposerJsonNormalizer(\sprintf(
            'file://%s',
            \realpath(__DIR__ . '/../../../Fixture/Vendor/Composer/schema.json'),
        ));

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringIdenticalToJsonString($scenario->normalized()->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<string, array{0: Test\Fixture\Vendor\Composer\ComposerJsonNormalizer\NormalizeNormalizesJson\Scenario}>
     */
    public static function provideScenarioWhereJsonIsValidAccordingToSchema(): iterable
    {
        $basePath = __DIR__ . '/../../../';

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../../../Fixture/Vendor/Composer/ComposerJsonNormalizer/NormalizeNormalizesJson'));

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

            $relativePath = \substr(
                $fileInfo->getPath(),
                \strlen($basePath),
            );

            $keyNotNormalized = \sprintf(
                '%s (not normalized)',
                $relativePath,
            );

            yield $keyNotNormalized => [
                Test\Fixture\Vendor\Composer\ComposerJsonNormalizer\NormalizeNormalizesJson\Scenario::create(
                    $keyNotNormalized,
                    Json::fromFile($originalFile),
                    Json::fromFile($normalizedFile),
                ),
            ];

            $keyAlreadyNormalized = \sprintf(
                '%s (already-normalized)',
                $relativePath,
            );

            yield $keyAlreadyNormalized => [
                Test\Fixture\Vendor\Composer\ComposerJsonNormalizer\NormalizeNormalizesJson\Scenario::create(
                    $keyAlreadyNormalized,
                    Json::fromFile($normalizedFile),
                    Json::fromFile($normalizedFile),
                ),
            ];
        }
    }
}
