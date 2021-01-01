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

namespace Ergebnis\Json\Normalizer\Test\Unit;

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Json;
use Ergebnis\Json\Normalizer\SchemaNormalizer;
use Ergebnis\Json\Normalizer\Validator\Result;
use Ergebnis\Json\Normalizer\Validator\SchemaValidator;
use Ergebnis\Json\Normalizer\Validator\SchemaValidatorInterface;
use JsonSchema\Exception\InvalidSchemaMediaTypeException;
use JsonSchema\Exception\JsonDecodingException;
use JsonSchema\Exception\ResourceNotFoundException;
use JsonSchema\Exception\UriResolverException;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use Prophecy\Argument;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\SchemaNormalizer
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\NormalizedInvalidAccordingToSchemaException
 * @uses \Ergebnis\Json\Normalizer\Exception\OriginalInvalidAccordingToSchemaException
 * @uses \Ergebnis\Json\Normalizer\Exception\SchemaUriCouldNotBeReadException
 * @uses \Ergebnis\Json\Normalizer\Exception\SchemaUriCouldNotBeResolvedException
 * @uses \Ergebnis\Json\Normalizer\Exception\SchemaUriReferencesDocumentWithInvalidMediaTypeException
 * @uses \Ergebnis\Json\Normalizer\Exception\SchemaUriReferencesInvalidJsonDocumentException
 * @uses \Ergebnis\Json\Normalizer\Json
 * @uses \Ergebnis\Json\Normalizer\Validator\Result
 * @uses \Ergebnis\Json\Normalizer\Validator\SchemaValidator
 */
final class SchemaNormalizerTest extends AbstractNormalizerTestCase
{
    public function testNormalizeThrowsSchemaUriCouldNotBeResolvedExceptionWhenSchemaUriCouldNotBeResolved(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON
        );

        $schemaUri = self::faker()->url;

        $schemaStorage = $this->prophesize(SchemaStorage::class);

        $schemaStorage
            ->getSchema(Argument::is($schemaUri))
            ->shouldBeCalled()
            ->willThrow(new UriResolverException());

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage->reveal(),
            $this->prophesize(SchemaValidatorInterface::class)->reveal()
        );

        $this->expectException(Exception\SchemaUriCouldNotBeResolvedException::class);

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsSchemaUriCouldNotBeReadExceptionWhenSchemaUriReferencesUnreadableResource(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON
        );

        $schemaUri = self::faker()->url;

        $schemaStorage = $this->prophesize(SchemaStorage::class);

        $schemaStorage
            ->getSchema(Argument::is($schemaUri))
            ->shouldBeCalled()
            ->willThrow(new ResourceNotFoundException());

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage->reveal(),
            $this->prophesize(SchemaValidatorInterface::class)->reveal()
        );

        $this->expectException(Exception\SchemaUriCouldNotBeReadException::class);

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsSchemaUriReferencesDocumentWithInvalidMediaTypeExceptionWhenSchemaUriReferencesResourceWithInvalidMediaType(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON
        );

        $schemaUri = self::faker()->url;

        $schemaStorage = $this->prophesize(SchemaStorage::class);

        $schemaStorage
            ->getSchema(Argument::is($schemaUri))
            ->shouldBeCalled()
            ->willThrow(new InvalidSchemaMediaTypeException());

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage->reveal(),
            $this->prophesize(SchemaValidatorInterface::class)->reveal()
        );

        $this->expectException(Exception\SchemaUriReferencesDocumentWithInvalidMediaTypeException::class);

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsRuntimeExceptionIfSchemaUriReferencesResourceWithInvalidJson(): void
    {
        $json = Json::fromEncoded(
            <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON
        );

        $schemaUri = self::faker()->url;

        $schemaStorage = $this->prophesize(SchemaStorage::class);

        $schemaStorage
            ->getSchema($schemaUri)
            ->shouldBeCalled()
            ->willThrow(new JsonDecodingException());

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage->reveal(),
            $this->prophesize(SchemaValidatorInterface::class)->reveal()
        );

        $this->expectException(Exception\SchemaUriReferencesInvalidJsonDocumentException::class);

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsOriginalInvalidAccordingToSchemaExceptionWhenOriginalNotValidAccordingToSchema(): void
    {
        $faker = self::faker();

        $json = Json::fromEncoded(
            <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON
        );

        $schemaUri = $faker->url;

        $schema = <<<'JSON'
{
    "type": "array"
}
JSON;

        $schemaDecoded = \json_decode($schema);

        $schemaStorage = $this->prophesize(SchemaStorage::class);

        $schemaStorage
            ->getSchema(Argument::is($schemaUri))
            ->shouldBeCalled()
            ->willReturn($schemaDecoded);

        $schemaValidator = $this->prophesize(SchemaValidatorInterface::class);

        $schemaValidator
            ->validate(
                Argument::is($json->decoded()),
                Argument::is($schemaDecoded)
            )
            ->shouldBeCalled()
            ->willReturn(Result::create(
                $faker->sentence,
                $faker->sentence,
                $faker->sentence
            ));

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage->reveal(),
            $schemaValidator->reveal()
        );

        $this->expectException(Exception\OriginalInvalidAccordingToSchemaException::class);

        $normalizer->normalize($json);
    }

    public function testNormalizeThrowsNormalizedInvalidAccordingToSchemaExceptionWhenNormalizedNotValidAccordingToSchema(): void
    {
        $faker = self::faker();

        $json = Json::fromEncoded(
            <<<'JSON'
{
    "url": "https://localheinz.com",
    "name": "Andreas Möller"
}
JSON
        );

        $schemaUri = $faker->url;

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

        $normalized = Json::fromEncoded(
            <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON
        );

        $schemaDecoded = \json_decode($schema);

        $schemaStorage = $this->prophesize(SchemaStorage::class);

        $schemaStorage
            ->getSchema(Argument::is($schemaUri))
            ->shouldBeCalled()
            ->willReturn($schemaDecoded);

        $schemaValidator = $this->prophesize(SchemaValidatorInterface::class);

        $schemaValidator
            ->validate(
                Argument::is($json->decoded()),
                Argument::is($schemaDecoded)
            )
            ->shouldBeCalled()
            ->will(function () use ($schemaValidator, $normalized, $schemaDecoded, $faker): Result {
                $schemaValidator
                    ->validate(
                        Argument::exact($normalized->decoded()),
                        Argument::is($schemaDecoded)
                    )
                    ->shouldBeCalled()
                    ->willReturn(Result::create(
                        $faker->sentence,
                        $faker->sentence,
                        $faker->sentence
                    ));

                return Result::create();
            });

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            $schemaStorage->reveal(),
            $schemaValidator->reveal()
        );

        $this->expectException(Exception\NormalizedInvalidAccordingToSchemaException::class);

        $normalizer->normalize($json);
    }

    /**
     * @dataProvider provideExpectedEncodedAndSchemaUri
     */
    public function testNormalizeNormalizes(string $expected, string $encoded, string $schemaUri): void
    {
        $json = Json::fromEncoded($encoded);

        $normalizer = new SchemaNormalizer(
            $schemaUri,
            new SchemaStorage(),
            new SchemaValidator(new Validator())
        );

        $normalized = $normalizer->normalize($json);

        self::assertSame($expected, $normalized->encoded());
    }

    /**
     * @return \Generator<array<string>>
     */
    public function provideExpectedEncodedAndSchemaUri(): \Generator
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

            if (!\is_string($jsonFile)) {
                throw new \RuntimeException(\sprintf(
                    'Unable to deduce JSON file name from normalized file name "%s".',
                    $normalizedFile
                ));
            }

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

            if (!\is_string($schemaFile)) {
                throw new \RuntimeException(\sprintf(
                    'Unable to deduce  file name from normalized file name "%s".',
                    $normalizedFile
                ));
            }

            if (!\file_exists($schemaFile)) {
                throw new \RuntimeException(\sprintf(
                    'Expected "%s" to exist, but it does not.',
                    $schemaFile
                ));
            }

            $expected = self::jsonFromFile($normalizedFile);
            $json = self::jsonFromFile($jsonFile);
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

    private static function jsonFromFile(string $file): string
    {
        $json = \file_get_contents($file);

        if (!\is_string($json)) {
            throw new \RuntimeException(\sprintf(
                'Unable to read content from file "%s".',
                $file
            ));
        }

        $decoded = \json_decode($json);

        if (null === $decoded && \JSON_ERROR_NONE !== \json_last_error()) {
            throw new \RuntimeException(\sprintf(
                'File "%s" does not contain valid JSON.',
                $file
            ));
        }

        $encoded = \json_encode($decoded);

        if (!\is_string($encoded)) {
            throw new \RuntimeException(\sprintf(
                'Unable to re-encode content from file "%s".',
                $file
            ));
        }

        return $encoded;
    }
}
