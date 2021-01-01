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

namespace Ergebnis\Json\Normalizer;

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
        SchemaStorage $schemaStorage,
        Validator\SchemaValidatorInterface $schemaValidator
    ) {
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

        $resultBeforeNormalization = $this->schemaValidator->validate(
            $decoded,
            $schema
        );

        if (!$resultBeforeNormalization->isValid()) {
            throw Exception\OriginalInvalidAccordingToSchemaException::fromSchemaUriAndErrors(
                $this->schemaUri,
                ...$resultBeforeNormalization->errors()
            );
        }

        $normalized = $this->normalizeData(
            $decoded,
            $schema
        );

        $resultAfterNormalization = $this->schemaValidator->validate(
            $normalized,
            $schema
        );

        if (!$resultAfterNormalization->isValid()) {
            throw Exception\NormalizedInvalidAccordingToSchemaException::fromSchemaUriAndErrors(
                $this->schemaUri,
                ...$resultAfterNormalization->errors()
            );
        }

        /** @var string $encoded */
        $encoded = \json_encode($normalized);

        return Json::fromEncoded($encoded);
    }

    /**
     * @param null|array<mixed>|bool|float|int|\stdClass|string $data
     *
     * @throws \InvalidArgumentException
     *
     * @return null|array<mixed>|bool|float|int|\stdClass|string
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

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    private function normalizeArray(array $data, \stdClass $schema): array
    {
        $schema = $this->resolveSchema(
            $data,
            $schema
        );

        if (!self::describesType('array', $schema)) {
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

        if (!self::describesType('object', $schema)) {
            return $data;
        }

        if (!\property_exists($schema, 'properties')) {
            return $data;
        }

        $normalized = new \stdClass();

        /** @var array<string, \stdClass> $objectPropertiesThatAreDefinedBySchema */
        $objectPropertiesThatAreDefinedBySchema = \array_intersect_key(
            \get_object_vars($schema->properties),
            \get_object_vars($data)
        );

        foreach ($objectPropertiesThatAreDefinedBySchema as $name => $valueSchema) {
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

        if (0 < \count($remainingProperties)) {
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
                $result = $this->schemaValidator->validate(
                    $data,
                    $oneOfSchema
                );

                if ($result->isValid()) {
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

    private static function describesType(string $type, \stdClass $schema): bool
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
