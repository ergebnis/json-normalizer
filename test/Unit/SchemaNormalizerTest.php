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
use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\SchemaNormalizer;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\SchemaValidator;
use JsonSchema\Exception\InvalidSchemaMediaTypeException;
use JsonSchema\Exception\JsonDecodingException;
use JsonSchema\Exception\ResourceNotFoundException;
use JsonSchema\Exception\UriResolverException;
use JsonSchema\SchemaStorage;

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
 */
final class SchemaNormalizerTest extends AbstractNormalizerTestCase
{
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
        );

        $this->expectException(Exception\OriginalInvalidAccordingToSchema::class);

        $normalizer->normalize($json);
    }

    /**
     * @dataProvider provideScenario
     */
    public function testNormalizeNormalizes(Test\Util\SchemaNormalizer\Scenario $scenario): void
    {
        $json = $scenario->original();

        $normalizer = new SchemaNormalizer(
            $scenario->schemaUri(),
            new SchemaStorage(),
            new SchemaValidator\SchemaValidator(),
        );

        $normalized = $normalizer->normalize($json);

        self::assertSame($scenario->normalized()->encoded(), $normalized->encoded());
    }

    /**
     * @return \Generator<string, array{0: Test\Util\SchemaNormalizer\Scenario}>
     */
    public function provideScenario(): \Generator
    {
        $basePath = __DIR__ . '/../';

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../Fixture/SchemaNormalizer/NormalizeNormalizes'));

        foreach ($iterator as $fileInfo) {
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

            $schemaFile = \preg_replace(
                '/normalized\.json$/',
                'schema.json',
                $normalizedFile,
            );

            if (!\is_string($schemaFile)) {
                throw new \RuntimeException(\sprintf(
                    'Unable to deduce schema JSON file name from normalized JSON file name "%s".',
                    $normalizedFile,
                ));
            }

            if (!\file_exists($schemaFile)) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to exist, but it does not.',
                    $schemaFile,
                ));
            }

            $normalized = self::jsonFromFile($normalizedFile);
            $original = self::jsonFromFile($originalFile);
            $schemaUri = \sprintf(
                'file://%s',
                $schemaFile,
            );

            $key = \substr(
                $fileInfo->getPath(),
                \strlen($basePath),
            );

            yield $key => [
                Test\Util\SchemaNormalizer\Scenario::create(
                    $normalized,
                    $original,
                    $schemaUri,
                ),
            ];
        }
    }

    private static function jsonFromFile(string $file): Json
    {
        $json = \file_get_contents($file);

        if (!\is_string($json)) {
            throw new \RuntimeException(\sprintf(
                'Unable to read content from file "%s".',
                $file,
            ));
        }

        $decoded = \json_decode($json);

        if (null === $decoded && \JSON_ERROR_NONE !== \json_last_error()) {
            throw new \RuntimeException(\sprintf(
                'File "%s" does not contain valid JSON.',
                $file,
            ));
        }

        $encoded = \json_encode($decoded);

        if (!\is_string($encoded)) {
            throw new \RuntimeException(\sprintf(
                'Unable to re-encode content from file "%s".',
                $file,
            ));
        }

        return Json::fromString($encoded);
    }
}
