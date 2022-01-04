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

use Ergebnis\Json\SchemaValidator;
use JsonSchema\Exception\InvalidSchemaMediaTypeException;
use JsonSchema\Exception\JsonDecodingException;
use JsonSchema\Exception\ResourceNotFoundException;
use JsonSchema\Exception\UriResolverException;
use JsonSchema\SchemaStorage;

final class SchemaNormalizer implements NormalizerInterface
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
            SchemaValidator\Json::fromString($json->encoded()),
            SchemaValidator\Json::fromString(\json_encode($schema)),
            SchemaValidator\JsonPointer::empty(),
        );

        if (!$resultBeforeNormalization->isValid()) {
            throw Exception\OriginalInvalidAccordingToSchemaException::fromSchemaUriAndErrors(
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
            SchemaValidator\JsonPointer::empty(),
        );

        if (!$resultAfterNormalization->isValid()) {
            throw Exception\NormalizedInvalidAccordingToSchemaException::fromSchemaUriAndErrors(
                $this->schemaUri,
                ...\array_map(static function (SchemaValidator\ValidationError $error): string {
                    return $error->message()->toString();
                }, $resultAfterNormalization->errors()),
            );
        }

        return $normalized;
    }

    /**
     * @param null|array<mixed>|bool|float|int|\stdClass|string $data
     *
     * @throws \InvalidArgumentException
     *
     * @return null|array<mixed>|bool|float|int|\stdClass|string
     */
    private function normalizeData(
        $data,
        \stdClass $schema
    ) {
        if (\is_array($data)) {
            return $this->normalizeArray(
                $data,
                $schema,
            );
        }

        if ($data instanceof \stdClass) {
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
        \stdClass $schema
    ): array {
        $schema = $this->resolveSchema(
            $data,
            $schema,
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
                    $itemSchema,
                );
            }, $data, $itemSchema);
        }

        /**
         * @see https://spacetelescope.github.io/understanding-json-schema/reference/array.html#list-validation
         */
        return \array_map(function ($item) use ($itemSchema) {
            return $this->normalizeData(
                $item,
                $itemSchema,
            );
        }, $data);
    }

    private function normalizeObject(
        \stdClass $data,
        \stdClass $schema
    ): \stdClass {
        $schema = $this->resolveSchema(
            $data,
            $schema,
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

        $remainingProperties = \get_object_vars($data);

        if (0 < \count($remainingProperties)) {
            \ksort($remainingProperties);

            foreach ($remainingProperties as $name => $value) {
                $normalized->{$name} = $value;
            }
        }

        return $normalized;
    }

    private function resolveSchema(
        $data,
        \stdClass $schema
    ): \stdClass {
        /**
         * @see https://spacetelescope.github.io/understanding-json-schema/reference/combining.html#anyof
         */
        if (
            \property_exists($schema, 'anyOf')
            && \is_array($schema->anyOf)
        ) {
            foreach ($schema->anyOf as $anyOfSchema) {
                $result = $this->schemaValidator->validate(
                    SchemaValidator\Json::fromString(\json_encode($data)),
                    SchemaValidator\Json::fromString(\json_encode($anyOfSchema)),
                    SchemaValidator\JsonPointer::empty(),
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
         * @see https://spacetelescope.github.io/understanding-json-schema/reference/combining.html#oneof
         */
        if (
            \property_exists($schema, 'oneOf')
            && \is_array($schema->oneOf)
        ) {
            foreach ($schema->oneOf as $oneOfSchema) {
                $result = $this->schemaValidator->validate(
                    SchemaValidator\Json::fromString(\json_encode($data)),
                    SchemaValidator\Json::fromString(\json_encode($oneOfSchema)),
                    SchemaValidator\JsonPointer::empty(),
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
         * @see https://spacetelescope.github.io/understanding-json-schema/structuring.html#reuse
         */
        if (
            \property_exists($schema, '$ref')
            && \is_string($schema->{'$ref'})
        ) {
            /** @var \stdClass $referenceSchema */
            $referenceSchema = $this->schemaStorage->resolveRefSchema($schema);

            return $this->resolveSchema(
                $data,
                $referenceSchema,
            );
        }

        return $schema;
    }

    private static function describesType(
        string $type,
        \stdClass $schema
    ): bool {
        if (!\property_exists($schema, 'type')) {
            return false;
        }

        if ($schema->type === $type) {
            return true;
        }

        return \is_array($schema->type) && \in_array($type, $schema->type, true);
    }
}
