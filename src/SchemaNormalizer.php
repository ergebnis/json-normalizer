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
     * @param array|\stdClass $data
     * @param \stdClass       $schema
     *
     * @throws \InvalidArgumentException
     *
     * @return array|bool|int|string
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

    private function normalizeArray(array $array, \stdClass $arraySchema): array
    {
        if (!$this->hasItemDefinition($arraySchema)) {
            return $array;
        }

        $itemSchema = $arraySchema->items;

        return \array_map(function ($item) use ($itemSchema) {
            return $this->normalizeData(
                $item,
                $itemSchema
            );
        }, $array);
    }

    private function normalizeObject(\stdClass $object, \stdClass $objectSchema): \stdClass
    {
        if ($this->hasReferenceDefinition($objectSchema)) {
            $objectSchema = $this->schemaStorage->resolveRefSchema($objectSchema);
        }

        if (!$this->hasPropertyDefinitions($objectSchema)) {
            return $object;
        }

        $normalized = new \stdClass();

        /** @var \stdClass[] $objectProperties */
        $objectProperties = \array_intersect_key(
            \get_object_vars($objectSchema->properties),
            \get_object_vars($object)
        );

        foreach ($objectProperties as $name => $valueSchema) {
            if ($valueSchema instanceof \stdClass && $this->hasReferenceDefinition($valueSchema)) {
                $valueSchema = $this->schemaStorage->resolveRefSchema($valueSchema);
            }

            $value = $object->{$name};

            if ($valueSchema instanceof \stdClass && !\is_scalar($value)) {
                $value = $this->normalizeData(
                    $value,
                    $valueSchema
                );
            }

            $normalized->{$name} = $value;

            unset($object->{$name});
        }

        $remainingProperties = \get_object_vars($object);

        if (\count($remainingProperties)) {
            \ksort($remainingProperties);

            foreach ($remainingProperties as $name => $value) {
                $normalized->{$name} = $value;
            }
        }

        return $normalized;
    }

    private function hasPropertyDefinitions(\stdClass $schema): bool
    {
        return $this->describesType('object', $schema)
            && \property_exists($schema, 'properties')
            && $schema->properties instanceof \stdClass;
    }

    private function hasItemDefinition(\stdClass $schema): bool
    {
        return $this->describesType('array', $schema)
            && \property_exists($schema, 'items')
            && $schema->items instanceof \stdClass;
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

    private function hasReferenceDefinition(\stdClass $schema): bool
    {
        return \property_exists($schema, '$ref') && \is_string($schema->{'$ref'});
    }
}
