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

namespace Ergebnis\Json\Normalizer\Test\Unit\Vendor\Composer;

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Normalizer\Vendor;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\ComposerJsonNormalizer
 * @covers \Ergebnis\Json\Normalizer\Vendor\Composer\PackageHashNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\ChainNormalizer
 * @uses \Ergebnis\Json\Normalizer\Exception\OriginalInvalidAccordingToSchema
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 * @uses \Ergebnis\Json\Normalizer\SchemaNormalizer
 * @uses \Ergebnis\Json\Normalizer\Vendor\Composer\VersionConstraintNormalizer
 * @uses \Ergebnis\Json\Normalizer\WithFinalNewLineNormalizer
 */
final class ComposerJsonNormalizerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider provideScenarioWhereJsonIsInvalidAccordingToSchema
     */
    public function testNormalizeRejectsJsonWhenItIsInvalidAccordingToSchema(Test\Fixture\Vendor\Composer\Scenario $scenario): void
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
     * @return \Generator<string, array{0: Test\Fixture\Vendor\Composer\Scenario}>
     */
    public static function provideScenarioWhereJsonIsInvalidAccordingToSchema(): \Generator
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
                $normalizedFile = $originalFile;
            }

            $key = \substr(
                $fileInfo->getPath(),
                \strlen($basePath),
            );

            yield $key => [
                Test\Fixture\Vendor\Composer\Scenario::create(
                    $key,
                    Json::fromFile($originalFile),
                    Json::fromFile($normalizedFile),
                ),
            ];
        }
    }

    /**
     * @dataProvider provideScenarioWhereJsonIsValidAccordingToSchema
     */
    public function testNormalizeNormalizesJsonWhenItIsValidAccordingToSchema(Test\Fixture\Vendor\Composer\Scenario $scenario): void
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
     * @return \Generator<string, array{0: Test\Fixture\Vendor\Composer\Scenario}>
     */
    public static function provideScenarioWhereJsonIsValidAccordingToSchema(): \Generator
    {
        $basePath = __DIR__ . '/../../../';

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../../../Fixture/Vendor/Composer/ComposerJsonNormalizer/NormalizeNormalizes'));

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
                $normalizedFile = $originalFile;
            }

            $key = \substr(
                $fileInfo->getPath(),
                \strlen($basePath),
            );

            yield $key => [
                Test\Fixture\Vendor\Composer\Scenario::create(
                    $key,
                    Json::fromFile($originalFile),
                    Json::fromFile($normalizedFile),
                ),
            ];
        }
    }
}
