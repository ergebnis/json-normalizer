<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit;

use JsonSchema\Exception;
use JsonSchema\SchemaStorage;
use Localheinz\Json\Normalizer\SchemaNormalizer;
use Localheinz\Json\Normalizer\Validator\SchemaValidatorInterface;
use Prophecy\Argument;

/**
 * @internal
 */
final class SchemaNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeThrowsRuntimeExceptionIfSchemaUriCouldNotBeResolved(): void
    {
        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $schemaUri = $this->faker()->url;

        $schemaStorage = $this->prophesize(SchemaStorage::class);

        $schemaStorage
            ->getSchema(Argument::is($schemaUri))
            ->shouldBeCalled()
            ->willThrow(new Exception\UriResolverException());

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage->reveal(),
            $this->prophesize(SchemaValidatorInterface::class)->reveal()
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Schema URI "%s" could not be resolved.',
            $schemaUri
        ));

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsRuntimeExceptionIfSchemaUriReferencesUnreadableResource(): void
    {
        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $schemaUri = $this->faker()->url;

        $schemaStorage = $this->prophesize(SchemaStorage::class);

        $schemaStorage
            ->getSchema(Argument::is($schemaUri))
            ->shouldBeCalled()
            ->willThrow(new Exception\ResourceNotFoundException());

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage->reveal(),
            $this->prophesize(SchemaValidatorInterface::class)->reveal()
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Schema URI "%s" does not reference a document that could be read.',
            $schemaUri
        ));

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsRuntimeExceptionIfSchemaUriReferencesResourceWithInvalidMediaType(): void
    {
        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $schemaUri = $this->faker()->url;

        $schemaStorage = $this->prophesize(SchemaStorage::class);

        $schemaStorage
            ->getSchema(Argument::is($schemaUri))
            ->shouldBeCalled()
            ->willThrow(new Exception\InvalidSchemaMediaTypeException());

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage->reveal(),
            $this->prophesize(SchemaValidatorInterface::class)->reveal()
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Schema URI "%s" does not reference a document with media type "application/schema+json".',
            $schemaUri
        ));

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsRuntimeExceptionIfSchemaUriReferencesResourceWithInvalidJson(): void
    {
        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $schemaUri = $this->faker()->url;

        $schemaStorage = $this->prophesize(SchemaStorage::class);

        $schemaStorage
            ->getSchema($schemaUri)
            ->shouldBeCalled()
            ->willThrow(new Exception\JsonDecodingException());

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage->reveal(),
            $this->prophesize(SchemaValidatorInterface::class)->reveal()
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Schema URI "%s" does not reference a document with valid JSON syntax.',
            $schemaUri
        ));

        $normalizer->normalize($json);
    }

    public function testNormalizeRejectsInvalidJsonAccordingToSchema(): void
    {
        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $schemaUri = $this->faker()->url;

        $schema = <<<'JSON'
{
    "type": "array"
}
JSON;

        $jsonDecoded = \json_decode($json);
        $schemaDecoded = \json_decode($schema);

        $schemaStorage = $this->prophesize(SchemaStorage::class);

        $schemaStorage
            ->getSchema(Argument::is($schemaUri))
            ->shouldBeCalled()
            ->willReturn($schemaDecoded);

        $schemaValidator = $this->prophesize(SchemaValidatorInterface::class);

        $schemaValidator
            ->isValid(
                Argument::exact($jsonDecoded),
                Argument::is($schemaDecoded)
            )
            ->shouldBeCalled()
            ->willReturn(false);

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage->reveal(),
            $schemaValidator->reveal()
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Original is not valid according to schema "%s".',
            $schemaUri
        ));

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsRuntimeExceptionIfNormalizedIsInvalidAccordingToSchema(): void
    {
        $json = <<<'JSON'
{
    "url": "https://localheinz.com",
    "name": "Andreas Möller"
}
JSON;

        $schemaUri = $this->faker()->url;

        $schema = <<<'JSON'
{
    "type": "object",
    "additionalProperties": false,
    "properties": {
        "name" : {
            "type" : "string"
        },
        "role" : {
            "type" : "string"
        }
    }
}
JSON;

        $normalized = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $jsonDecoded = \json_decode($json);
        $schemaDecoded = \json_decode($schema);
        $normalizedDecoded = \json_decode($normalized);

        $schemaStorage = $this->prophesize(SchemaStorage::class);

        $schemaStorage
            ->getSchema(Argument::is($schemaUri))
            ->shouldBeCalled()
            ->willReturn($schemaDecoded);

        $schemaValidator = $this->prophesize(SchemaValidatorInterface::class);

        $schemaValidator
            ->isValid(
                Argument::exact($jsonDecoded),
                Argument::is($schemaDecoded)
            )
            ->shouldBeCalled()
            ->will(function () use ($schemaValidator, $normalizedDecoded, $schemaDecoded) {
                $schemaValidator
                    ->isValid(
                        Argument::exact($normalizedDecoded),
                        Argument::is($schemaDecoded)
                    )
                    ->shouldBeCalled()
                    ->willReturn(false);

                return true;
            });

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage->reveal(),
            $schemaValidator->reveal()
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Normalized is not valid according to schema "%s".',
            $schemaUri
        ));

        $normalizer->normalize($json);
    }

    /**
     * @dataProvider providerNormalizeNormalizes
     *
     * @param string $expected
     * @param string $json
     * @param string $schemaUri
     */
    public function testNormalizeNormalizes(string $expected, string $json, string $schemaUri): void
    {
        $normalizer = new SchemaNormalizer($schemaUri);

        $normalized = $normalizer->normalize($json);

        $this->assertSame($expected, $normalized);
    }

    public function providerNormalizeNormalizes(): \Generator
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

            $jsonFile = \preg_replace(
                '/normalized\.json$/',
                'original.json',
                $normalizedFile
            );

            if (!\file_exists($jsonFile)) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to exist, but it does not.',
                    $jsonFile
                ));
            }

            $schemaFile = \preg_replace(
                '/normalized\.json$/',
                'schema.json',
                $normalizedFile
            );

            if (!\file_exists($schemaFile)) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to exist, but it does not.',
                    $schemaFile
                ));
            }

            $expected = $this->jsonFromFile($normalizedFile);
            $json = $this->jsonFromFile($jsonFile);
            $schemaUri = \sprintf(
                'file://%s',
                $schemaFile
            );

            $key = \substr(
                $fileInfo->getPath(),
                \strlen($basePath)
            );

            yield $key => [
                $expected,
                $json,
                $schemaUri,
            ];
        }
    }

    private function jsonFromFile(string $file): string
    {
        $json = \file_get_contents($file);

        return \json_encode(\json_decode($json));
    }
}
