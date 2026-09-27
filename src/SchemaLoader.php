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

namespace Ergebnis\Json\Normalizer;

use Ergebnis\Json\Parser;
use JsonSchema\Constraints;
use JsonSchema\Exception\InvalidSchemaMediaTypeException;
use JsonSchema\Exception\JsonDecodingException;
use JsonSchema\Exception\ResourceNotFoundException;
use JsonSchema\Exception\UriResolverException;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

/**
 * @internal
 */
final class SchemaLoader
{
    private SchemaStorage $storage;
    private Parser\Printer $printer;

    private function __construct(SchemaStorage $storage)
    {
        $this->storage = $storage;
        $this->printer = new Parser\Printer();
    }

    public static function create(): self
    {
        return new self(new SchemaStorage());
    }

    public static function fromStorage(SchemaStorage $storage): self
    {
        return new self($storage);
    }

    /**
     * @throws Exception\SchemaUriCouldNotBeRead
     * @throws Exception\SchemaUriCouldNotBeResolved
     * @throws Exception\SchemaUriReferencesDocumentWithInvalidMediaType
     * @throws Exception\SchemaUriReferencesInvalidJsonDocument
     */
    public function load(string $uri): Schema
    {
        try {
            $schema = $this->storage->getSchema($uri);
        } catch (UriResolverException $exception) {
            throw Exception\SchemaUriCouldNotBeResolved::fromString($uri);
        } catch (ResourceNotFoundException $exception) {
            throw Exception\SchemaUriCouldNotBeRead::fromString($uri);
        } catch (InvalidSchemaMediaTypeException $exception) {
            throw Exception\SchemaUriReferencesDocumentWithInvalidMediaType::fromString($uri);
        } catch (JsonDecodingException $exception) {
            throw Exception\SchemaUriReferencesInvalidJsonDocument::fromString($uri);
        }

        return Schema::fromObject($schema);
    }

    /**
     * @return list<string>
     */
    public function errors(
        Schema $schema,
        Parser\Node\Node $node
    ): array {
        $data = \json_decode($this->printer->print(
            $node,
            Parser\Format::compact(),
        ));

        $validator = new Validator(new Constraints\Factory($this->storage));

        $validator->validate(
            $data,
            $schema->toObject(),
        );

        /** @var list<array{property: string, message: string}> $validationErrors */
        $validationErrors = $validator->getErrors();

        $errors = [];

        foreach ($validationErrors as $error) {
            if ('' === $error['property']) {
                $errors[] = $error['message'];

                continue;
            }

            $errors[] = \sprintf(
                '%s: %s',
                $error['property'],
                $error['message'],
            );
        }

        return $errors;
    }

    public function storage(): SchemaStorage
    {
        return $this->storage;
    }
}
