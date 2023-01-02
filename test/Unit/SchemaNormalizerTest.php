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
use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\SchemaNormalizer;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Pointer;
use Ergebnis\Json\SchemaValidator;
use JsonSchema\Exception\InvalidSchemaMediaTypeException;
use JsonSchema\Exception\JsonDecodingException;
use JsonSchema\Exception\ResourceNotFoundException;
use JsonSchema\Exception\UriResolverException;
use JsonSchema\SchemaStorage;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\SchemaNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\NormalizedInvalidAccordingToSchema
 * @uses \Ergebnis\Json\Normalizer\Exception\OriginalInvalidAccordingToSchema
 * @uses \Ergebnis\Json\Normalizer\Exception\SchemaUriCouldNotBeRead
 * @uses \Ergebnis\Json\Normalizer\Exception\SchemaUriCouldNotBeResolved
 * @uses \Ergebnis\Json\Normalizer\Exception\SchemaUriReferencesDocumentWithInvalidMediaType
 * @uses \Ergebnis\Json\Normalizer\Exception\SchemaUriReferencesInvalidJsonDocument
 * @uses \Ergebnis\Json\Normalizer\Format\JsonEncodeOptions
 */
final class SchemaNormalizerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testNormalizeThrowsSchemaUriCouldNotBeResolvedExceptionWhenSchemaUriCouldNotBeResolved(): void
    {
        $json = Json::fromString(
            <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON
        );

        $schemaUri = self::faker()->url();

        $schemaStorage = $this->createMock(SchemaStorage::class);

        $schemaStorage
            ->expects(self::once())
            ->method('getSchema')
            ->with(self::identicalTo($schemaUri))
            ->willThrowException(new UriResolverException());

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage,
            new SchemaValidator\SchemaValidator(),
            Pointer\Specification::never(),
        );

        $this->expectException(Exception\SchemaUriCouldNotBeResolved::class);

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsSchemaUriCouldNotBeReadExceptionWhenSchemaUriReferencesUnreadableResource(): void
    {
        $json = Json::fromString(
            <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON
        );

        $schemaUri = self::faker()->url();

        $schemaStorage = $this->createMock(SchemaStorage::class);

        $schemaStorage
            ->expects(self::once())
            ->method('getSchema')
            ->with(self::identicalTo($schemaUri))
            ->willThrowException(new ResourceNotFoundException());

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage,
            new SchemaValidator\SchemaValidator(),
            Pointer\Specification::never(),
        );

        $this->expectException(Exception\SchemaUriCouldNotBeRead::class);

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsSchemaUriReferencesDocumentWithInvalidMediaTypeExceptionWhenSchemaUriReferencesResourceWithInvalidMediaType(): void
    {
        $json = Json::fromString(
            <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON
        );

        $schemaUri = self::faker()->url();

        $schemaStorage = $this->createMock(SchemaStorage::class);

        $schemaStorage
            ->expects(self::once())
            ->method('getSchema')
            ->with(self::identicalTo($schemaUri))
            ->willThrowException(new InvalidSchemaMediaTypeException());

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage,
            new SchemaValidator\SchemaValidator(),
            Pointer\Specification::never(),
        );

        $this->expectException(Exception\SchemaUriReferencesDocumentWithInvalidMediaType::class);

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsRuntimeExceptionIfSchemaUriReferencesResourceWithInvalidJson(): void
    {
        $json = Json::fromString(
            <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON
        );

        $schemaUri = self::faker()->url();

        $schemaStorage = $this->createMock(SchemaStorage::class);

        $schemaStorage
            ->expects(self::once())
            ->method('getSchema')
            ->with(self::identicalTo($schemaUri))
            ->willThrowException(new JsonDecodingException());

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage,
            new SchemaValidator\SchemaValidator(),
            Pointer\Specification::never(),
        );

        $this->expectException(Exception\SchemaUriReferencesInvalidJsonDocument::class);

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsOriginalInvalidAccordingToSchemaExceptionWhenOriginalNotValidAccordingToSchema(): void
    {
        $faker = self::faker();

        $json = Json::fromString(
            <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON
        );

        $schemaUri = $faker->url();

        $schema = <<<'JSON'
{
    "type": "array"
}
JSON;

        $schemaDecoded = \json_decode($schema);

        $schemaStorage = $this->createMock(SchemaStorage::class);

        $schemaStorage
            ->expects(self::once())
            ->method('getSchema')
            ->with(self::identicalTo($schemaUri))
            ->willReturn($schemaDecoded);

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage,
            new SchemaValidator\SchemaValidator(),
            Pointer\Specification::never(),
        );

        $this->expectException(Exception\OriginalInvalidAccordingToSchema::class);

        $normalizer->normalize($json);
    }

    /**
     * @dataProvider provideScenarioWithCustomJsonPointerSpecification
     * @dataProvider provideScenarioWithDefaultJsonPointerSpecification
     */
    public function testNormalizeNormalizes(Test\Fixture\SchemaNormalizer\Scenario $scenario): void
    {
        $json = $scenario->original();

        $normalizer = new SchemaNormalizer(
            $scenario->schemaUri(),
            new SchemaStorage(),
            new SchemaValidator\SchemaValidator(),
            $scenario->specificationForPointerToDataThatShouldNotBeSorted(),
        );

        $normalized = $normalizer->normalize($json);

        self::assertJsonStringIdenticalToJsonString($scenario->normalized()->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<string, array{0: Test\Fixture\SchemaNormalizer\Scenario}>
     */
    public static function provideScenarioWithDefaultJsonPointerSpecification(): \Generator
    {
        $basePath = __DIR__ . '/../';

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../Fixture/SchemaNormalizer/NormalizeNormalizes/WithDefaultJsonPointerSpecification'));

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
                    'Unable to deduce normalzied JSON file name from original JSON file name "%s".',
                    $originalFile,
                ));
            }

            if (!\file_exists($normalizedFile)) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to exist, but it does not.',
                    $normalizedFile,
                ));
            }

            $schemaFile = \preg_replace(
                '/original\.json$/',
                'schema.json',
                $originalFile,
            );

            if (!\is_string($schemaFile)) {
                throw new \RuntimeException(\sprintf(
                    'Unable to deduce schema JSON file name from original JSON file name "%s".',
                    $originalFile,
                ));
            }

            if (!\file_exists($schemaFile)) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to exist, but it does not.',
                    $schemaFile,
                ));
            }

            $key = \substr(
                $fileInfo->getPath(),
                \strlen($basePath),
            );

            yield $key => [
                Test\Fixture\SchemaNormalizer\Scenario::create(
                    $key,
                    \sprintf(
                        'file://%s',
                        $schemaFile,
                    ),
                    Pointer\Specification::never(),
                    Json::fromFile($originalFile),
                    Json::fromFile($normalizedFile),
                ),
            ];
        }
    }

    /**
     * @return \Generator<string, array{0: Test\Fixture\SchemaNormalizer\Scenario}>
     */
    public static function provideScenarioWithCustomJsonPointerSpecification(): \Generator
    {
        $basePath = __DIR__ . '/../';

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../Fixture/SchemaNormalizer/NormalizeNormalizes/WithCustomJsonPointerSpecification'));

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

            $schemaFile = \preg_replace(
                '/original\.json$/',
                'schema.json',
                $originalFile,
            );

            if (!\is_string($schemaFile)) {
                throw new \RuntimeException(\sprintf(
                    'Unable to deduce schema JSON file name from original JSON file name "%s".',
                    $originalFile,
                ));
            }

            if (!\file_exists($schemaFile)) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to exist, but it does not.',
                    $schemaFile,
                ));
            }

            $jsonPointerSpecificationFile = \preg_replace(
                '/original\.json$/',
                'specification-for-pointer-to-data-that-should-not-be-sorted.php',
                $originalFile,
            );

            if (!\is_string($jsonPointerSpecificationFile)) {
                throw new \RuntimeException(\sprintf(
                    'Unable to deduce JSON pointer specification file name from original JSON file name "%s".',
                    $originalFile,
                ));
            }

            if (!\file_exists($jsonPointerSpecificationFile)) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to exist, but it does not.',
                    $jsonPointerSpecificationFile,
                ));
            }

            $specificationForPointerToDataThatShouldBeSorted = include $jsonPointerSpecificationFile;

            if (!$specificationForPointerToDataThatShouldBeSorted instanceof Pointer\Specification) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to return an instance of "%s", got "%s" instead.',
                    $jsonPointerSpecificationFile,
                    Pointer\Specification::class,
                    \get_debug_type($specificationForPointerToDataThatShouldBeSorted),
                ));
            }

            $key = \substr(
                $fileInfo->getPath(),
                \strlen($basePath),
            );

            yield $key => [
                Test\Fixture\SchemaNormalizer\Scenario::create(
                    $key,
                    \sprintf(
                        'file://%s',
                        $schemaFile,
                    ),
                    $specificationForPointerToDataThatShouldBeSorted,
                    Json::fromFile($originalFile),
                    Json::fromFile($normalizedFile),
                ),
            ];
        }
    }
}
