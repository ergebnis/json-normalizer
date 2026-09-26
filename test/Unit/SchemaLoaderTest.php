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

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\SchemaLoader;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Parser;
use JsonSchema\Exception\InvalidSchemaMediaTypeException;
use JsonSchema\Exception\JsonDecodingException;
use JsonSchema\Exception\ResourceNotFoundException;
use JsonSchema\Exception\UriResolverException;
use JsonSchema\SchemaStorage;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\SchemaLoader
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\SchemaUriCouldNotBeRead
 * @uses \Ergebnis\Json\Normalizer\Exception\SchemaUriCouldNotBeResolved
 * @uses \Ergebnis\Json\Normalizer\Exception\SchemaUriReferencesDocumentWithInvalidMediaType
 * @uses \Ergebnis\Json\Normalizer\Exception\SchemaUriReferencesInvalidJsonDocument
 * @uses \Ergebnis\Json\Normalizer\Schema
 */
final class SchemaLoaderTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testLoadThrowsSchemaUriCouldNotBeResolvedWhenSchemaUriCouldNotBeResolved(): void
    {
        $schemaUri = self::faker()->url();

        $storage = $this->createMock(SchemaStorage::class);

        $storage->method('getSchema')->willThrowException(new UriResolverException());

        $loader = SchemaLoader::fromStorage($storage);

        $this->expectException(Exception\SchemaUriCouldNotBeResolved::class);

        $loader->load($schemaUri);
    }

    public function testLoadThrowsSchemaUriCouldNotBeReadWhenSchemaUriReferencesUnreadableResource(): void
    {
        $schemaUri = self::faker()->url();

        $storage = $this->createMock(SchemaStorage::class);

        $storage->method('getSchema')->willThrowException(new ResourceNotFoundException());

        $loader = SchemaLoader::fromStorage($storage);

        $this->expectException(Exception\SchemaUriCouldNotBeRead::class);

        $loader->load($schemaUri);
    }

    public function testLoadThrowsSchemaUriReferencesDocumentWithInvalidMediaTypeWhenSchemaUriReferencesResourceWithInvalidMediaType(): void
    {
        $schemaUri = self::faker()->url();

        $storage = $this->createMock(SchemaStorage::class);

        $storage->method('getSchema')->willThrowException(new InvalidSchemaMediaTypeException());

        $loader = SchemaLoader::fromStorage($storage);

        $this->expectException(Exception\SchemaUriReferencesDocumentWithInvalidMediaType::class);

        $loader->load($schemaUri);
    }

    public function testLoadThrowsSchemaUriReferencesInvalidJsonDocumentWhenSchemaUriReferencesResourceWithInvalidJson(): void
    {
        $schemaUri = self::faker()->url();

        $storage = $this->createMock(SchemaStorage::class);

        $storage->method('getSchema')->willThrowException(new JsonDecodingException());

        $loader = SchemaLoader::fromStorage($storage);

        $this->expectException(Exception\SchemaUriReferencesInvalidJsonDocument::class);

        $loader->load($schemaUri);
    }

    public function testLoadReturnsSchemaWhenSchemaUriReferencesSchema(): void
    {
        $loader = SchemaLoader::create();

        $schema = $loader->load(self::schemaUri());

        self::assertSame(['name'], $schema->propertyNames());
        self::assertTrue($schema->isRequired('name'));
    }

    public function testErrorsReturnsEmptyListWhenNodeIsValidAccordingToSchema(): void
    {
        $loader = SchemaLoader::create();

        $schema = $loader->load(self::schemaUri());

        $node = self::parse('{"name":"ergebnis/json-normalizer"}');

        $errors = $loader->errors(
            $schema,
            $node,
        );

        self::assertSame([], $errors);
    }

    public function testErrorsReturnsErrorsWhenNodeIsNotValidAccordingToSchema(): void
    {
        $loader = SchemaLoader::create();

        $schema = $loader->load(self::schemaUri());

        $node = self::parse('{"name":9000}');

        $expected = [
            'name: Integer value found, but a string is required',
        ];

        $errors = $loader->errors(
            $schema,
            $node,
        );

        self::assertSame($expected, $errors);
    }

    public function testErrorsReturnsErrorWithoutPropertyWhenRootIsNotValidAccordingToSchema(): void
    {
        $loader = SchemaLoader::create();

        $schema = $loader->load(self::schemaUri());

        $node = self::parse('[]');

        $expected = [
            'Array value found, but an object is required',
        ];

        $errors = $loader->errors(
            $schema,
            $node,
        );

        self::assertSame($expected, $errors);
    }

    public function testStorageReturnsStorage(): void
    {
        $storage = new SchemaStorage();

        $loader = SchemaLoader::fromStorage($storage);

        self::assertSame($storage, $loader->storage());
    }

    private static function schemaUri(): string
    {
        return \sprintf(
            'file://%s',
            \realpath(\sprintf(
                '%s/../Fixture/Schema/NameIsRequiredString/schema.json',
                __DIR__,
            )),
        );
    }

    private static function parse(string $json): Parser\Node\Node
    {
        $parser = new Parser\Parser();

        return $parser->parse(
            Parser\Raw::fromString($json),
            Parser\MaximumDepth::default(),
        );
    }
}
