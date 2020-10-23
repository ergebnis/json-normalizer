<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Validator;

use Ergebnis\Json\Normalizer\Validator\SchemaValidator;
use Ergebnis\Json\Normalizer\Validator\SchemaValidatorInterface;
use Ergebnis\Test\Util\Helper;
use JsonSchema\Validator;
use PHPUnit\Framework;
use Prophecy\Argument;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Validator\SchemaValidator
 *
 * @uses \Ergebnis\Json\Normalizer\Validator\Result
 */
final class SchemaValidatorTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsSchemaValidatorInterface(): void
    {
        self::assertClassImplementsInterface(SchemaValidatorInterface::class, SchemaValidator::class);
    }

    /**
     * @dataProvider \Ergebnis\Json\Normalizer\Test\DataProvider\Boolean::provideBoolean()
     */
    public function testIsValidUsesSchemaValidator(bool $isValid): void
    {
        $dataJson = <<<'JSON'
{
    "foo": "bar"
    "baz": [
        9000,
        123
    ]
}
JSON;

        $schemaJson = <<<'JSON'
{
    "type": "object"
}
JSON;

        $data = \json_decode($dataJson);
        $schema = \json_decode($schemaJson);

        $schemaValidator = $this->prophesize(Validator::class);

        $schemaValidator
            ->reset()
            ->shouldBeCalled();

        $schemaValidator
            ->check(
                Argument::is($data),
                Argument::is($schema)
            )
            ->shouldBeCalled();

        $schemaValidator
            ->isValid()
            ->shouldBeCalled()
            ->willReturn($isValid);

        $validator = new SchemaValidator($schemaValidator->reveal());

        self::assertSame($isValid, $validator->isValid($data, $schema));
    }

    public function testValidateReturnsResultWhenDataIsNotValidAccordingToSchema(): void
    {
        $dataJson = <<<'JSON'
{
    "number": 1600,
    "street_name": "Pennsylvania",
    "street_type": "Avenue",
    "direction": "NW"
}
JSON;

        $schemaJson = <<<'JSON'
{
    "type": "object",
    "properties": {
        "name": {
            "type": "string"
        },
        "email": {
            "type": "string"
        },
        "address": {
            "type": "string"
        },
        "telephone": {
            "type": "string"
        }
    },
    "required": [
        "name",
        "email"
    ],
    "additionalProperties": false
}
JSON;

        $data = \json_decode($dataJson);
        $schema = \json_decode($schemaJson);

        $validator = new SchemaValidator(new Validator());

        $result = $validator->validate(
            $data,
            $schema
        );

        self::assertFalse($result->isValid());

        $errors = $result->errors();

        $expected = [
            'name: The property name is required',
            'email: The property email is required',
            'The property number is not defined and the definition does not allow additional properties',
            'The property street_name is not defined and the definition does not allow additional properties',
            'The property street_type is not defined and the definition does not allow additional properties',
            'The property direction is not defined and the definition does not allow additional properties',
        ];

        \sort($errors);
        \sort($expected);

        self::assertEquals($expected, $errors);
    }

    public function testValidateReturnsResultWhenDataIsValidAccordingToSchema(): void
    {
        $dataJson = <<<'JSON'
{
    "name": "Jane Doe",
    "email": "jane.doe@example.org"
}
JSON;

        $schemaJson = <<<'JSON'
{
    "type": "object",
    "properties": {
        "name": {
            "type": "string"
        },
        "email": {
            "type": "string"
        },
        "address": {
            "type": "string"
        },
        "telephone": {
            "type": "string"
        }
    },
    "required": [
        "name",
        "email"
    ],
    "additionalProperties": false
}
JSON;

        $data = \json_decode($dataJson);
        $schema = \json_decode($schemaJson);

        $validator = new SchemaValidator(new Validator());

        $result = $validator->validate(
            $data,
            $schema
        );

        self::assertTrue($result->isValid());
        self::assertSame([], $result->errors());
    }
}
