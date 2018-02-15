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
use JsonSchema\Exception;
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

    public function normalize(string $json): string
    {
        $decoded = \json_decode($json);

        if (null === $decoded && JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(\sprintf(
                '"%s" is not valid JSON.',
                $json
            ));
        }

        try {
            $schema = $this->schemaStorage->getSchema($this->schemaUri);
        } catch (Exception\UriResolverException $exception) {
            throw new \RuntimeException(\sprintf(
                'Schema URI "%s" could not be resolved.',
                $this->schemaUri
            ));
        } catch (Exception\ResourceNotFoundException $exception) {
            throw new \RuntimeException(\sprintf(
                'Schema URI "%s" does not reference a document that could be read.',
                $this->schemaUri
            ));
        } catch (Exception\InvalidSchemaMediaTypeException $exception) {
            throw new \RuntimeException(\sprintf(
                'Schema URI "%s" does not reference a document with media type "application/schema+json".',
                $this->schemaUri
            ));
        } catch (Exception\JsonDecodingException $exception) {
            throw new \RuntimeException(\sprintf(
                'Schema URI "%s" does not reference a document with valid JSON syntax.',
                $this->schemaUri
            ));
        }

        if (!$this->schemaValidator->isValid($decoded, $schema)) {
            throw new \InvalidArgumentException(\sprintf(
                'Original is not valid according to schema "%s".',
                $this->schemaUri
            ));
        }

        $normalized = $this->normalizeData(
            $decoded,
            $schema
        );

        if (!$this->schemaValidator->isValid($normalized, $schema)) {
            throw new \RuntimeException(\sprintf(
                'Normalized is not valid according to schema "%s".',
                $this->schemaUri
            ));
        }

        return \json_encode($normalized);
    }

    /**
     * @param array|bool|float|int|\stdClass|string $data
     * @param \stdClass                             $schema
     *
     * @throws \InvalidArgumentException
     *
     * @return array|bool|float|int|\stdClass|string
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
            return $this->resolveSchema(
                $data,
                $this->schemaStorage->resolveRefSchema($schema)
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
