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
 */
final class SchemaValidatorTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsSchemaValidatorInterface(): void
    {
        self::assertClassImplementsInterface(SchemaValidatorInterface::class, SchemaValidator::class);
    }

    /**
     * @dataProvider \Ergebnis\Json\Normalizer\Test\Util\DataProvider\Boolean::provideBoolean()
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
}
