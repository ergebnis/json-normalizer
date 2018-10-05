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

namespace Localheinz\Json\Normalizer;

use JsonSchema\Constraints;
use JsonSchema\Exception\InvalidSchemaMediaTypeException;
use JsonSchema\Exception\JsonDecodingException;
use JsonSchema\Exception\ResourceNotFoundException;
use JsonSchema\Exception\UriResolverException;
use JsonSchema\SchemaStorage;

final class SchemaNormalizer implements NormalizerInterface
{
    /**
     * @var string
     */
    private $schemaUri;

    /**
     * @var SchemaStorage
     */
    private $schemaStorage;

    /**
     * @var Validator\SchemaValidatorInterface
     */
    private $schemaValidator;

    public function __construct(
        string $schemaUri,
        SchemaStorage $schemaStorage = null,
        Validator\SchemaValidatorInterface $schemaValidator = null
    ) {
        if (null === $schemaStorage) {
            $schemaStorage = new SchemaStorage();
        }

        if (null === $schemaValidator) {
            $schemaValidator = new Validator\SchemaValidator(new \JsonSchema\Validator(new Constraints\Factory(
                $schemaStorage,
                $schemaStorage->getUriRetriever()
            )));
        }

        $this->schemaUri = $schemaUri;
        $this->schemaStorage = $schemaStorage;
        $this->schemaValidator = $schemaValidator;
    }

    public function normalize(Json $json): Json
    {
        $decoded = $json->decoded();

        try {
            /** @var \stdClass $schema */
            $schema = $this->schemaStorage->getSchema($this->schemaUri);
        } catch (UriResolverException $exception) {
            throw Exception\SchemaUriCouldNotBeResolvedException::fromSchemaUri($this->schemaUri);
        } catch (ResourceNotFoundException $exception) {
            throw Exception\SchemaUriCouldNotBeReadException::fromSchemaUri($this->schemaUri);
        } catch (InvalidSchemaMediaTypeException $exception) {
            throw Exception\SchemaUriReferencesDocumentWithInvalidMediaTypeException::fromSchemaUri($this->schemaUri);
        } catch (JsonDecodingException $exception) {
            throw Exception\SchemaUriReferencesInvalidJsonDocumentException::fromSchemaUri($this->schemaUri);
        }

        if (!$this->schemaValidator->isValid($decoded, $schema)) {
            throw Exception\OriginalInvalidAccordingToSchemaException::fromSchemaUri($this->schemaUri);
        }

        $normalized = $this->normalizeData(
            $decoded,
            $schema
        );

        if (!$this->schemaValidator->isValid($normalized, $schema)) {
            throw Exception\NormalizedInvalidAccordingToSchemaException::fromSchemaUri($this->schemaUri);
        }

        /** @var string $encoded */
        $encoded = \json_encode($normalized);

        return Json::fromEncoded($encoded);
    }

    /**
     * @param null|array|bool|float|int|\stdClass|string $data
     * @param \stdClass                                  $schema
     *
     * @throws \InvalidArgumentException
     *
     * @return null|array|bool|float|int|\stdClass|string
     */
    private function normalizeData($data, \stdClass $schema)
    {
        if (\is_array($data)) {
            return $this->normalizeArray(
                $data,
                $schema
            );
        }

        if ($data instanceof \stdClass) {
            return $this->normalizeObject(
                $data,
                $schema
            );
        }

        return $data;
    }

    private function normalizeArray(array $data, \stdClass $schema): array
    {
        $schema = $this->resolveSchema(
            $data,
            $schema
        );

        if (!$this->describesType('array', $schema)) {
            return $data;
        }

        if (!\property_exists($schema, 'items')) {
            return $data;
        }

        $itemSchema = $schema->items;

        /**
         * @see https://spacetelescope.github.io/understanding-json-schema/reference/array.html#tuple-validation
         */
        if (\is_array($itemSchema)) {
            return \array_map(function ($item, \stdClass $itemSchema) {
                return $this->normalizeData(
                    $item,
                    $itemSchema
                );
            }, $data, $itemSchema);
        }

        /**
         * @see https://spacetelescope.github.io/understanding-json-schema/reference/array.html#list-validation
         */
        return \array_map(function ($item) use ($itemSchema) {
            return $this->normalizeData(
                $item,
                $itemSchema
            );
        }, $data);
    }

    private function normalizeObject(\stdClass $data, \stdClass $schema): \stdClass
    {
        $schema = $this->resolveSchema(
            $data,
            $schema
        );

        if (!$this->describesType('object', $schema)) {
            return $data;
        }

        if (!\property_exists($schema, 'properties')) {
            return $data;
        }

        $normalized = new \stdClass();

        /** @var \stdClass[] $objectProperties */
        $objectProperties = \array_intersect_key(
            \get_object_vars($schema->properties),
            \get_object_vars($data)
        );

        foreach ($objectProperties as $name => $valueSchema) {
            $value = $data->{$name};

            $valueSchema = $this->resolveSchema(
                $value,
                $valueSchema
            );

            $normalized->{$name} = $this->normalizeData(
                $value,
                $valueSchema
            );

            unset($data->{$name});
        }

        $remainingProperties = \get_object_vars($data);

        if (\count($remainingProperties)) {
            \ksort($remainingProperties);

            foreach ($remainingProperties as $name => $value) {
                $normalized->{$name} = $value;
            }
        }

        return $normalized;
    }

    private function resolveSchema($data, \stdClass $schema): \stdClass
    {
        /**
         * @see https://spacetelescope.github.io/understanding-json-schema/reference/combining.html#oneof
         */
        if (\property_exists($schema, 'oneOf') && \is_array($schema->oneOf)) {
            foreach ($schema->oneOf as $oneOfSchema) {
                if ($this->schemaValidator->isValid($data, $oneOfSchema)) {
                    return $this->resolveSchema(
                        $data,
                        $oneOfSchema
                    );
                }
            }
        }

        /**
         * @see https://spacetelescope.github.io/understanding-json-schema/structuring.html#reuse
         */
        if (\property_exists($schema, '$ref') && \is_string($schema->{'$ref'})) {
            /** @var \stdClass $referenceSchema */
            $referenceSchema = $this->schemaStorage->resolveRefSchema($schema);

            return $this->resolveSchema(
                $data,
                $referenceSchema
            );
        }

        return $schema;
    }

    private function describesType(string $type, \stdClass $schema): bool
    {
        if (!\property_exists($schema, 'type')) {
            return false;
        }

        if ($schema->type === $type) {
            return true;
        }

        return \is_array($schema->type) && \in_array($type, $schema->type, true);
    }
}
