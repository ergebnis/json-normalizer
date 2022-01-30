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

namespace Ergebnis\Json\Normalizer;

use Ergebnis\Json\Pointer;
use Ergebnis\Json\SchemaValidator;
use JsonSchema\Exception\InvalidSchemaMediaTypeException;
use JsonSchema\Exception\JsonDecodingException;
use JsonSchema\Exception\ResourceNotFoundException;
use JsonSchema\Exception\UriResolverException;
use JsonSchema\SchemaStorage;

final class SchemaNormalizer implements Normalizer
{
    private string $schemaUri;
    private SchemaStorage $schemaStorage;
    private SchemaValidator\SchemaValidator $schemaValidator;

    public function __construct(
        string $schemaUri,
        SchemaStorage $schemaStorage,
        SchemaValidator\SchemaValidator $schemaValidator
    ) {
        $this->schemaUri = $schemaUri;
        $this->schemaStorage = $schemaStorage;
        $this->schemaValidator = $schemaValidator;
    }

    public function normalize(Json $json): Json
    {
        try {
            $schema = $this->schemaStorage->getSchema($this->schemaUri);
        } catch (UriResolverException $exception) {
            throw Exception\SchemaUriCouldNotBeResolved::fromSchemaUri($this->schemaUri);
        } catch (ResourceNotFoundException $exception) {
            throw Exception\SchemaUriCouldNotBeRead::fromSchemaUri($this->schemaUri);
        } catch (InvalidSchemaMediaTypeException $exception) {
            throw Exception\SchemaUriReferencesDocumentWithInvalidMediaType::fromSchemaUri($this->schemaUri);
        } catch (JsonDecodingException $exception) {
            throw Exception\SchemaUriReferencesInvalidJsonDocument::fromSchemaUri($this->schemaUri);
        }

        $resultBeforeNormalization = $this->schemaValidator->validate(
            SchemaValidator\Json::fromString($json->encoded()),
            SchemaValidator\Json::fromString(\json_encode($schema)),
            Pointer\JsonPointer::document(),
        );

        if (!$resultBeforeNormalization->isValid()) {
            throw Exception\OriginalInvalidAccordingToSchema::fromSchemaUriAndErrors(
                $this->schemaUri,
                ...\array_map(static function (SchemaValidator\ValidationError $error): string {
                    return $error->message()->toString();
                }, $resultBeforeNormalization->errors()),
            );
        }

        $normalized = Json::fromEncoded(\json_encode($this->normalizeData(
            $json->decoded(),
            $schema,
        )));

        $resultAfterNormalization = $this->schemaValidator->validate(
            SchemaValidator\Json::fromString($normalized->encoded()),
            SchemaValidator\Json::fromString(\json_encode($schema)),
            Pointer\JsonPointer::document(),
        );

        if (!$resultAfterNormalization->isValid()) {
            throw Exception\NormalizedInvalidAccordingToSchema::fromSchemaUriAndErrors(
                $this->schemaUri,
                ...\array_map(static function (SchemaValidator\ValidationError $error): string {
                    return $error->message()->toString();
                }, $resultAfterNormalization->errors()),
            );
        }

        return $normalized;
    }

    /**
     * @param null|array<mixed>|bool|float|int|object|string $data
     *
     * @throws \InvalidArgumentException
     *
     * @return null|array<mixed>|bool|float|int|object|string
     */
    private function normalizeData(
        $data,
        object $schema
    ) {
        if (\is_array($data)) {
            return $this->normalizeArray(
                $data,
                $schema,
            );
        }

        if (\is_object($data)) {
            return $this->normalizeObject(
                $data,
                $schema,
            );
        }

        return $data;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    private function normalizeArray(
        array $data,
        object $schema
    ): array {
        $schema = $this->resolveSchema(
            $data,
            $schema,
        );

        $itemSchema = new \stdClass();

        /**
         * @see https://json-schema.org/understanding-json-schema/reference/array.html#items
         */
        if (\property_exists($schema, 'items')) {
            $itemSchema = $schema->items;

            /**
             * @see https://json-schema.org/understanding-json-schema/reference/array.html#tuple-validation
             */
            if (\is_array($itemSchema)) {
                return \array_map(function ($item, object $itemSchema) {
                    return $this->normalizeData(
                        $item,
                        $itemSchema,
                    );
                }, $data, $itemSchema);
            }
        }

        /**
         * @see https://json-schema.org/understanding-json-schema/reference/array.html#list-validation
         */
        return \array_map(function ($item) use ($itemSchema) {
            return $this->normalizeData(
                $item,
                $itemSchema,
            );
        }, $data);
    }

    private function normalizeObject(
        object $data,
        object $schema
    ): object {
        $schema = $this->resolveSchema(
            $data,
            $schema,
        );

        $normalized = new \stdClass();

        /**
         * @see https://json-schema.org/understanding-json-schema/reference/object.html#properties
         */
        if (
            \property_exists($schema, 'properties')
            && \is_object($schema->properties)
        ) {
            /** @var array<string, object> $objectPropertiesThatAreDefinedBySchema */
            $objectPropertiesThatAreDefinedBySchema = \array_intersect_key(
                \get_object_vars($schema->properties),
                \get_object_vars($data),
            );

            foreach ($objectPropertiesThatAreDefinedBySchema as $name => $valueSchema) {
                $value = $data->{$name};

                $valueSchema = $this->resolveSchema(
                    $value,
                    $valueSchema,
                );

                $normalized->{$name} = $this->normalizeData(
                    $value,
                    $valueSchema,
                );

                unset($data->{$name});
            }
        }

        $additionalProperties = \get_object_vars($data);

        if ([] === $additionalProperties) {
            return $normalized;
        }

        \ksort($additionalProperties);

        $valueSchema = new \stdClass();

        foreach ($additionalProperties as $name => $value) {
            $normalized->{$name} = $this->normalizeData(
                $value,
                $valueSchema,
            );
        }

        return $normalized;
    }

    private function resolveSchema(
        $data,
        object $schema
    ): object {
        /**
         * @see https://json-schema.org/understanding-json-schema/reference/combining.html#anyof
         */
        if (
            \property_exists($schema, 'anyOf')
            && \is_array($schema->anyOf)
        ) {
            foreach ($schema->anyOf as $anyOfSchema) {
                $result = $this->schemaValidator->validate(
                    SchemaValidator\Json::fromString(\json_encode($data)),
                    SchemaValidator\Json::fromString(\json_encode($anyOfSchema)),
                    Pointer\JsonPointer::document(),
                );

                if ($result->isValid()) {
                    return $this->resolveSchema(
                        $data,
                        $anyOfSchema,
                    );
                }
            }
        }

        /**
         * @see https://json-schema.org/understanding-json-schema/reference/combining.html#oneof
         */
        if (
            \property_exists($schema, 'oneOf')
            && \is_array($schema->oneOf)
        ) {
            foreach ($schema->oneOf as $oneOfSchema) {
                $result = $this->schemaValidator->validate(
                    SchemaValidator\Json::fromString(\json_encode($data)),
                    SchemaValidator\Json::fromString(\json_encode($oneOfSchema)),
                    Pointer\JsonPointer::document(),
                );

                if ($result->isValid()) {
                    return $this->resolveSchema(
                        $data,
                        $oneOfSchema,
                    );
                }
            }
        }

        /**
         * @see https://json-schema.org/understanding-json-schema/structuring.html#reuse
         */
        if (
            \property_exists($schema, '$ref')
            && \is_string($schema->{'$ref'})
        ) {
            $referenceSchema = $this->schemaStorage->resolveRefSchema($schema);

            return $this->resolveSchema(
                $data,
                $referenceSchema,
            );
        }

        return $schema;
    }
}
