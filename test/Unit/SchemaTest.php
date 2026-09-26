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
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Schema
 */
final class SchemaTest extends Framework\TestCase
{
    public function testFromObjectReturnsSchema(): void
    {
        $object = (object) [
            'type' => 'object',
        ];

        $schema = Schema::fromObject($object);

        self::assertSame($object, $schema->toObject());
    }

    public function testPropertyNamesReturnsEmptyListWhenSchemaHasNoProperties(): void
    {
        $schema = Schema::fromObject((object) [
            'type' => 'object',
        ]);

        self::assertSame([], $schema->propertyNames());
    }

    public function testPropertyNamesReturnsEmptyListWhenPropertiesIsNotObject(): void
    {
        $schema = Schema::fromObject((object) [
            'properties' => true,
        ]);

        self::assertSame([], $schema->propertyNames());
    }

    public function testPropertyNamesReturnsNamesInSchemaOrderWhenSchemaHasProperties(): void
    {
        $schema = Schema::fromObject((object) [
            'properties' => (object) [
                'name' => (object) [],
                'description' => (object) [],
                '10' => (object) [],
                'authors' => (object) [],
            ],
        ]);

        $expected = [
            'name',
            'description',
            '10',
            'authors',
        ];

        self::assertSame($expected, $schema->propertyNames());
    }

    public function testIsRequiredReturnsFalseWhenSchemaHasNoRequiredProperties(): void
    {
        $schema = Schema::fromObject((object) [
            'properties' => (object) [
                'name' => (object) [],
            ],
        ]);

        self::assertFalse($schema->isRequired('name'));
    }

    public function testIsRequiredReturnsFalseWhenPropertyIsNotRequired(): void
    {
        $schema = Schema::fromObject((object) [
            'required' => [
                'name',
            ],
        ]);

        self::assertFalse($schema->isRequired('description'));
    }

    public function testIsRequiredReturnsTrueWhenPropertyIsRequired(): void
    {
        $schema = Schema::fromObject((object) [
            'required' => [
                'description',
                'name',
            ],
        ]);

        self::assertTrue($schema->isRequired('name'));
    }
}
