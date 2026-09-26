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

use Ergebnis\Json\Normalizer\Change;
use Ergebnis\Json\Normalizer\Configuration;
use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Runner;
use Ergebnis\Json\Normalizer\Test;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\RuleVisitor
 * @covers \Ergebnis\Json\Normalizer\Runner
 *
 * @uses \Ergebnis\Json\Normalizer\Change
 * @uses \Ergebnis\Json\Normalizer\Configuration
 * @uses \Ergebnis\Json\Normalizer\Context
 * @uses \Ergebnis\Json\Normalizer\Exception\InputInvalidAccordingToSchema
 * @uses \Ergebnis\Json\Normalizer\Exception\OutputInvalidAccordingToSchema
 * @uses \Ergebnis\Json\Normalizer\Exception\RulesDidNotSettle
 * @uses \Ergebnis\Json\Normalizer\Result
 * @uses \Ergebnis\Json\Normalizer\Rule\Action
 * @uses \Ergebnis\Json\Normalizer\Rule\Name
 * @uses \Ergebnis\Json\Normalizer\Rule\Target
 * @uses \Ergebnis\Json\Normalizer\Schema
 * @uses \Ergebnis\Json\Normalizer\SchemaLoader
 */
final class RunnerTest extends Framework\TestCase
{
    public function testNormalizeThrowsInvalidJsonWhenRawIsNotValidJson(): void
    {
        $raw = Parser\Raw::fromString('{"bin":');

        $runner = Runner::create(Configuration::create());

        $this->expectException(Parser\InvalidJson::class);

        $runner->normalize($raw);
    }

    public function testNormalizeThrowsRootNodeCanNotBeRemovedWhenRuleRemovesRoot(): void
    {
        $raw = Parser\Raw::fromString('{"bin":["b","a"]}');

        $runner = Runner::create(Configuration::create()->withRules(Test\Double\Rule\RemovingRule::create(
            Rule\Name::fromString('remove'),
            Rule\Target::create(
                Pointer\Specification::equals(Pointer\JsonPointer::document()),
                Parser\Node\ObjectNode::class,
            ),
        )));

        $this->expectException(Parser\Traverser\RootNodeCanNotBeRemoved::class);

        $runner->normalize($raw);
    }

    public function testNormalizeThrowsRulesDidNotSettleWhenRuleChangesNodeInEveryPass(): void
    {
        $raw = Parser\Raw::fromString('{"bin":[]}');

        $runner = Runner::create(Configuration::create()->withRules(Test\Double\Rule\AppendingRule::create(
            Rule\Name::fromString('append'),
            Rule\Target::create(
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
                Parser\Node\ArrayNode::class,
            ),
        )));

        $this->expectException(Exception\RulesDidNotSettle::class);
        $this->expectExceptionMessage(\sprintf(
            'Rules did not settle after %d passes; the last pass changed "append" at "/bin".',
            Runner::MAXIMUM_PASSES,
        ));

        $runner->normalize($raw);
    }

    public function testNormalizeThrowsInputInvalidAccordingToSchemaWhenInputIsInvalid(): void
    {
        $raw = Parser\Raw::fromString('{"name":9000}');

        $runner = Runner::create(Configuration::create()->withSchema(self::schemaUri()));

        $this->expectException(Exception\InputInvalidAccordingToSchema::class);

        $runner->normalize($raw);
    }

    public function testNormalizeThrowsOutputInvalidAccordingToSchemaWhenRuleMakesOutputInvalid(): void
    {
        $raw = Parser\Raw::fromString('{"name":"ergebnis/json-normalizer"}');

        $runner = Runner::create(Configuration::create()
            ->withRules(Test\Double\Rule\ReplacingRule::create(
                Rule\Name::fromString('replace'),
                Rule\Target::create(
                    Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/name')),
                    Parser\Node\StringNode::class,
                ),
                Parser\Node\NumberNode::fromInt(9000),
            ))
            ->withSchema(self::schemaUri()));

        $this->expectException(Exception\OutputInvalidAccordingToSchema::class);

        $runner->normalize($raw);
    }

    public function testNormalizeReturnsResultWhenInputAndOutputAreValidAccordingToSchema(): void
    {
        $raw = Parser\Raw::fromString('{"name":"ergebnis/json-normalizer"}');

        $runner = Runner::create(Configuration::create()->withSchema(self::schemaUri()));

        $result = $runner->normalize($raw);

        self::assertSame($raw->toString(), $result->output()->toString());
    }

    public function testNormalizeReturnsResultWithoutChangesWhenConfigurationHasNoRules(): void
    {
        $raw = Parser\Raw::fromString(<<<'JSON'
{
    "bin": [
        "b",
        "a"
    ]
}

JSON);

        $runner = Runner::create(Configuration::create());

        $result = $runner->normalize($raw);

        self::assertSame($raw, $result->input());
        self::assertSame($raw->toString(), $result->output()->toString());
        self::assertSame([], $result->changes());
        self::assertFalse($result->isChanged());
    }

    public function testNormalizeKeepsFormatOfInput(): void
    {
        $raw = Parser\Raw::fromString("{\n\t\"bin\": [\n\t\t\"b\",\n\t\t\"a\"\n\t]\n}");

        $runner = Runner::create(Configuration::create()->withRules(Test\Double\Rule\ReplacingRule::create(
            Rule\Name::fromString('replace'),
            Rule\Target::create(
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
                Parser\Node\ArrayNode::class,
            ),
            Parser\Node\ArrayNode::create(
                Parser\Node\StringNode::fromString('a'),
                Parser\Node\StringNode::fromString('b'),
            ),
        )));

        $result = $runner->normalize($raw);

        self::assertSame("{\n\t\"bin\": [\n\t\t\"a\",\n\t\t\"b\"\n\t]\n}", $result->output()->toString());
    }

    public function testNormalizePrintsWithConfiguredFormat(): void
    {
        $raw = Parser\Raw::fromString("{\n\t\"bin\": [\n\t\t\"b\",\n\t\t\"a\"\n\t]\n}");

        $runner = Runner::create(Configuration::create()
            ->withFinalNewLine(Parser\FinalNewLine::present())
            ->withIndent(Parser\Indent::create(
                Parser\IndentSize::fromInt(2),
                Parser\IndentStyle::space(),
            )));

        $result = $runner->normalize($raw);

        $expected = <<<'JSON'
{
  "bin": [
    "b",
    "a"
  ]
}

JSON;

        self::assertSame($expected, $result->output()->toString());
        self::assertSame([], $result->changes());
        self::assertTrue($result->isChanged());
    }

    public function testNormalizeReturnsResultWithChangeWhenRuleReplacesNode(): void
    {
        $raw = Parser\Raw::fromString('{"bin":["b","a"]}');

        $runner = Runner::create(Configuration::create()->withRules(Test\Double\Rule\ReplacingRule::create(
            Rule\Name::fromString('replace'),
            Rule\Target::create(
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
                Parser\Node\ArrayNode::class,
            ),
            Parser\Node\ArrayNode::create(
                Parser\Node\StringNode::fromString('a'),
                Parser\Node\StringNode::fromString('b'),
            ),
        )));

        $result = $runner->normalize($raw);

        $expected = [
            [
                'replace',
                '/bin',
            ],
        ];

        self::assertSame('{"bin":["a","b"]}', $result->output()->toString());
        self::assertTrue($result->isChanged());
        self::assertSame($expected, self::describe(...$result->changes()));
    }

    public function testNormalizeRemovesNodeWhenRuleRemovesIt(): void
    {
        $raw = Parser\Raw::fromString('{"bin":[],"name":"foo/bar"}');

        $runner = Runner::create(Configuration::create()->withRules(Test\Double\Rule\RemovingRule::create(
            Rule\Name::fromString('remove'),
            Rule\Target::create(
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
                Parser\Node\ArrayNode::class,
            ),
        )));

        $result = $runner->normalize($raw);

        $expected = [
            [
                'remove',
                '/bin',
            ],
        ];

        self::assertSame('{"name":"foo/bar"}', $result->output()->toString());
        self::assertSame($expected, self::describe(...$result->changes()));
    }

    public function testNormalizeDoesNotApplyRuleWhereSkipIsSatisfied(): void
    {
        $raw = Parser\Raw::fromString('{"bin":["b","a"]}');

        $runner = Runner::create(Configuration::create()
            ->withRules(Test\Double\Rule\RemovingRule::create(
                Rule\Name::fromString('remove'),
                Rule\Target::create(
                    Pointer\Specification::always(),
                    Parser\Node\ArrayNode::class,
                ),
            ))
            ->withSkip(
                Rule\Name::fromString('remove'),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
            ));

        $result = $runner->normalize($raw);

        self::assertSame('{"bin":["b","a"]}', $result->output()->toString());
        self::assertSame([], $result->changes());
    }

    public function testNormalizeAppliesOtherRulesWhereRuleIsSkipped(): void
    {
        $raw = Parser\Raw::fromString('{"bin":["b","a"]}');

        $runner = Runner::create(Configuration::create()
            ->withRules(
                Test\Double\Rule\RemovingRule::create(
                    Rule\Name::fromString('remove'),
                    Rule\Target::create(
                        Pointer\Specification::always(),
                        Parser\Node\ArrayNode::class,
                    ),
                ),
                Test\Double\Rule\ReplacingRule::create(
                    Rule\Name::fromString('replace'),
                    Rule\Target::create(
                        Pointer\Specification::always(),
                        Parser\Node\ArrayNode::class,
                    ),
                    Parser\Node\ArrayNode::create(
                        Parser\Node\StringNode::fromString('a'),
                        Parser\Node\StringNode::fromString('b'),
                    ),
                ),
            )
            ->withSkip(
                Rule\Name::fromString('remove'),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
            ));

        $result = $runner->normalize($raw);

        self::assertSame('{"bin":["a","b"]}', $result->output()->toString());
    }

    public function testNormalizeAppliesRuleAfterRuleWhoseTargetDoesNotMatch(): void
    {
        $raw = Parser\Raw::fromString('{"bin":["b","a"]}');

        $runner = Runner::create(Configuration::create()->withRules(
            Test\Double\Rule\KeepingRule::create(
                Rule\Name::fromString('keep'),
                Rule\Target::create(
                    Pointer\Specification::always(),
                    Parser\Node\StringNode::class,
                ),
            ),
            Test\Double\Rule\ReplacingRule::create(
                Rule\Name::fromString('replace'),
                Rule\Target::create(
                    Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
                    Parser\Node\ArrayNode::class,
                ),
                Parser\Node\ArrayNode::create(
                    Parser\Node\StringNode::fromString('a'),
                    Parser\Node\StringNode::fromString('b'),
                ),
            ),
        ));

        $result = $runner->normalize($raw);

        self::assertSame('{"bin":["a","b"]}', $result->output()->toString());
    }

    public function testNormalizeAppliesRuleAfterRuleThatKeepsNode(): void
    {
        $raw = Parser\Raw::fromString('{"bin":["b","a"]}');

        $runner = Runner::create(Configuration::create()->withRules(
            Test\Double\Rule\KeepingRule::create(
                Rule\Name::fromString('keep'),
                Rule\Target::create(
                    Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
                    Parser\Node\ArrayNode::class,
                ),
            ),
            Test\Double\Rule\ReplacingRule::create(
                Rule\Name::fromString('replace'),
                Rule\Target::create(
                    Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
                    Parser\Node\ArrayNode::class,
                ),
                Parser\Node\ArrayNode::create(
                    Parser\Node\StringNode::fromString('a'),
                    Parser\Node\StringNode::fromString('b'),
                ),
            ),
        ));

        $result = $runner->normalize($raw);

        self::assertSame('{"bin":["a","b"]}', $result->output()->toString());
    }

    public function testNormalizeAppliesRulesInOrderToSameNode(): void
    {
        $raw = Parser\Raw::fromString('{"bin":["b","a"]}');

        $runner = Runner::create(Configuration::create()->withRules(
            Test\Double\Rule\ReplacingRule::create(
                Rule\Name::fromString('replace'),
                Rule\Target::create(
                    Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
                    Parser\Node\ArrayNode::class,
                ),
                Parser\Node\ArrayNode::create(),
            ),
            Test\Double\Rule\RemovingRule::create(
                Rule\Name::fromString('remove'),
                Rule\Target::create(
                    Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
                    Parser\Node\ArrayNode::class,
                ),
            ),
        ));

        $result = $runner->normalize($raw);

        $expected = [
            [
                'replace',
                '/bin',
            ],
            [
                'remove',
                '/bin',
            ],
        ];

        self::assertSame('{}', $result->output()->toString());
        self::assertSame($expected, self::describe(...$result->changes()));
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    private static function describe(Change ...$changes): array
    {
        return \array_map(static function (Change $change): array {
            return [
                $change->rule()->toString(),
                $change->path()->toJsonPointer()->toJsonString(),
            ];
        }, $changes);
    }

    private static function schemaUri(): string
    {
        return \sprintf(
            'file://%s',
            \realpath(\sprintf(
                '%s/../Fixture/Schema/NameIsRequiredString/schema.json',
                __DIR__,
            )),
        );
    }
}
