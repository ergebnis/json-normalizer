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

namespace Localheinz\Json\Normalizer\Test\Unit\Validator;

use JsonSchema\Validator;
use Localheinz\Json\Normalizer\Validator\SchemaValidator;
use Localheinz\Json\Normalizer\Validator\SchemaValidatorInterface;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;
use Prophecy\Argument;

/**
 * @internal
 */
final class SchemaValidatorTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsSchemaValidatorInterface(): void
    {
        $this->assertClassImplementsInterface(SchemaValidatorInterface::class, SchemaValidator::class);
    }

    /**
     * @dataProvider providerIsValid
     *
     * @param bool $isValid
     */
    public function testValidateUsesSchemaValidator(bool $isValid): void
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
                Argument::exact($data),
                Argument::exact($schema)
            )
            ->shouldBeCalled();

        $schemaValidator
            ->isValid()
            ->shouldBeCalled()
            ->willReturn($isValid);

        $validator = new SchemaValidator($schemaValidator->reveal());

        $this->assertSame($isValid, $validator->isValid($data, $schema));
    }

    public function providerIsValid(): \Generator
    {
        $values = [
            'is-valid' => true,
            'is-not-valid' => false,
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }
}
