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

use Ergebnis\Json\Normalizer\Schema;
use Ergebnis\Json\Normalizer\SchemaLoader;
use Ergebnis\Json\Normalizer\SchemaResolver;
use Ergebnis\Json\Parser;
use JsonSchema\SchemaStorage;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\SchemaResolver
 *
 * @uses \Ergebnis\Json\Normalizer\Schema
 * @uses \Ergebnis\Json\Normalizer\SchemaLoader
 */
final class SchemaResolverTest extends Framework\TestCase
{
    public function testResolveReturnsSchemaWhenSchemaHasNeitherCombinationsNorReference(): void
    {
        $schema = Schema::fromObject((object) [
            'type' => 'string',
        ]);

        $resolver = self::resolver();

        $resolved = $resolver->resolve(
            $schema,
            self::parse('"foo"'),
        );

        self::assertSame($schema, $resolved);
    }

    public function testResolveReturnsSchemaOfFirstMatchingBranchWhenSchemaHasAnyOf(): void
    {
        $resolver = self::resolver();

        $resolved = $resolver->resolve(
            Schema::fromObject((object) [
                'anyOf' => [
                    (object) [
                        'type' => 'string',
                    ],
                    (object) [
                        'type' => 'object',
                        'title' => 'first',
                    ],
                    (object) [
                        'type' => 'object',
                        'title' => 'second',
                    ],
                ],
            ]),
            self::parse('{}'),
        );

        $expected = (object) [
            'type' => 'object',
            'title' => 'first',
        ];

        self::assertEquals($expected, $resolved->toObject());
    }

    public function testResolveReturnsSchemaWhenNoBranchOfAnyOfMatches(): void
    {
        $schema = Schema::fromObject((object) [
            'anyOf' => [
                (object) [
                    'type' => 'string',
                ],
            ],
        ]);

        $resolver = self::resolver();

        $resolved = $resolver->resolve(
            $schema,
            self::parse('{}'),
        );

        self::assertSame($schema, $resolved);
    }

    public function testResolveReturnsSchemaOfFirstMatchingBranchWhenSchemaHasOneOf(): void
    {
        $resolver = self::resolver();

        $resolved = $resolver->resolve(
            Schema::fromObject((object) [
                'oneOf' => [
                    (object) [
                        'type' => 'array',
                    ],
                    (object) [
                        'type' => 'string',
                        'title' => 'first',
                    ],
                    (object) [
                        'type' => 'string',
                        'title' => 'second',
                    ],
                ],
            ]),
            self::parse('"foo"'),
        );

        $expected = (object) [
            'type' => 'string',
            'title' => 'first',
        ];

        self::assertEquals($expected, $resolved->toObject());
    }

    public function testResolveReturnsReferencedSchemaWhenSchemaHasReference(): void
    {
        $storage = new SchemaStorage();

        $storage->addSchema('https://example.org/schema.json', (object) [
            'definitions' => (object) [
                'name' => (object) [
                    'type' => 'string',
                ],
            ],
        ]);

        $resolver = SchemaResolver::create(SchemaLoader::fromStorage($storage));

        $resolved = $resolver->resolve(
            Schema::fromObject((object) [
                '$ref' => 'https://example.org/schema.json#/definitions/name',
            ]),
            self::parse('"foo"'),
        );

        $expected = (object) [
            'type' => 'string',
        ];

        self::assertEquals($expected, $resolved->toObject());
    }

    public function testResolveReturnsReferencedSchemaWhenBranchOfAnyOfHasReference(): void
    {
        $storage = new SchemaStorage();

        $storage->addSchema('https://example.org/schema.json', (object) [
            'definitions' => (object) [
                'name' => (object) [
                    'type' => 'string',
                    'title' => 'name',
                ],
            ],
        ]);

        $resolver = SchemaResolver::create(SchemaLoader::fromStorage($storage));

        $resolved = $resolver->resolve(
            Schema::fromObject((object) [
                'anyOf' => [
                    (object) [
                        'type' => 'array',
                    ],
                    (object) [
                        '$ref' => 'https://example.org/schema.json#/definitions/name',
                    ],
                ],
            ]),
            self::parse('"foo"'),
        );

        $expected = (object) [
            'type' => 'string',
            'title' => 'name',
        ];

        self::assertEquals($expected, $resolved->toObject());
    }

    public function testPropertyReturnsSchemaOfPropertyWhenSchemaHasProperty(): void
    {
        $resolver = self::resolver();

        $property = $resolver->property(
            Schema::fromObject((object) [
                'properties' => (object) [
                    'name' => (object) [
                        'type' => 'string',
                    ],
                ],
                'additionalProperties' => (object) [
                    'type' => 'boolean',
                ],
            ]),
            'name',
        );

        $expected = (object) [
            'type' => 'string',
        ];

        self::assertEquals($expected, $property->toObject());
    }

    public function testPropertyReturnsSchemaOfPropertyWhenNameLooksNumeric(): void
    {
        $resolver = self::resolver();

        $property = $resolver->property(
            Schema::fromObject(\json_decode('{"properties":{"10":{"type":"string"}}}')),
            '10',
        );

        $expected = (object) [
            'type' => 'string',
        ];

        self::assertEquals($expected, $property->toObject());
    }

    public function testPropertyReturnsAdditionalPropertiesWhenSchemaDoesNotHavePropertyAndAdditionalPropertiesIsObject(): void
    {
        $resolver = self::resolver();

        $property = $resolver->property(
            Schema::fromObject((object) [
                'properties' => (object) [
                    'name' => (object) [
                        'type' => 'string',
                    ],
                ],
                'additionalProperties' => (object) [
                    'type' => 'boolean',
                ],
            ]),
            'license',
        );

        $expected = (object) [
            'type' => 'boolean',
        ];

        self::assertEquals($expected, $property->toObject());
    }

    public function testPropertyReturnsEmptySchemaWhenSchemaDoesNotHavePropertyAndAdditionalPropertiesIsNotObject(): void
    {
        $resolver = self::resolver();

        $property = $resolver->property(
            Schema::fromObject((object) [
                'additionalProperties' => true,
            ]),
            'license',
        );

        self::assertEquals(new \stdClass(), $property->toObject());
    }

    public function testElementReturnsItemsWhenItemsIsObject(): void
    {
        $resolver = self::resolver();

        $element = $resolver->element(
            Schema::fromObject((object) [
                'items' => (object) [
                    'type' => 'string',
                ],
            ]),
            3,
        );

        $expected = (object) [
            'type' => 'string',
        ];

        self::assertEquals($expected, $element->toObject());
    }

    public function testElementReturnsItemAtIndexWhenItemsIsTuple(): void
    {
        $resolver = self::resolver();

        $element = $resolver->element(
            Schema::fromObject((object) [
                'items' => [
                    (object) [
                        'type' => 'string',
                    ],
                    (object) [
                        'type' => 'boolean',
                    ],
                ],
            ]),
            1,
        );

        $expected = (object) [
            'type' => 'boolean',
        ];

        self::assertEquals($expected, $element->toObject());
    }

    public function testElementReturnsEmptySchemaWhenItemsIsTupleWithoutItemAtIndex(): void
    {
        $resolver = self::resolver();

        $element = $resolver->element(
            Schema::fromObject((object) [
                'items' => [
                    (object) [
                        'type' => 'string',
                    ],
                ],
            ]),
            1,
        );

        self::assertEquals(new \stdClass(), $element->toObject());
    }

    public function testElementReturnsEmptySchemaWhenSchemaDoesNotHaveItems(): void
    {
        $resolver = self::resolver();

        $element = $resolver->element(
            Schema::fromObject((object) [
                'type' => 'array',
            ]),
            0,
        );

        self::assertEquals(new \stdClass(), $element->toObject());
    }

    private static function resolver(): SchemaResolver
    {
        return SchemaResolver::create(SchemaLoader::create());
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
