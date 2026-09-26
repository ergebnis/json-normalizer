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

namespace Ergebnis\Json\Normalizer\Test\Unit\Rule;

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Rule\Target
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidTarget
 */
final class TargetTest extends Framework\TestCase
{
    public function testCreateThrowsInvalidTargetWhenNodeClassesAreEmpty(): void
    {
        $location = Pointer\Specification::always();

        $this->expectException(Exception\InvalidTarget::class);
        $this->expectExceptionMessage('Target must name at least one node class.');

        Rule\Target::create($location);
    }

    public function testCreateThrowsInvalidTargetWhenNodeClassDoesNotImplementNode(): void
    {
        $location = Pointer\Specification::always();

        $this->expectException(Exception\InvalidTarget::class);
        $this->expectExceptionMessage(\sprintf(
            'Target node class "%s" must implement "%s".',
            \stdClass::class,
            Parser\Node\Node::class,
        ));

        Rule\Target::create(
            $location,
            Parser\Node\ArrayNode::class,
            \stdClass::class,
        );
    }

    public function testCreateReturnsTargetWhenNodeClassesImplementNode(): void
    {
        $location = Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin'));

        $target = Rule\Target::create(
            $location,
            Parser\Node\ArrayNode::class,
            Parser\Node\ObjectNode::class,
        );

        $expected = [
            Parser\Node\ArrayNode::class,
            Parser\Node\ObjectNode::class,
        ];

        self::assertSame($location, $target->location());
        self::assertSame($expected, $target->nodeClasses());
    }

    public function testMatchesReturnsFalseWhenNodeIsNotInstanceOfNodeClass(): void
    {
        $node = Parser\Node\ObjectNode::create();
        $path = Parser\Traverser\Path::root();

        $target = Rule\Target::create(
            Pointer\Specification::always(),
            Parser\Node\ArrayNode::class,
        );

        self::assertFalse($target->matches(
            $node,
            $path,
        ));
    }

    public function testMatchesReturnsFalseWhenPathDoesNotSatisfyLocation(): void
    {
        $node = Parser\Node\ArrayNode::create();
        $path = Parser\Traverser\Path::root()->property(
            Parser\Index::fromInt(0),
            Parser\Node\StringNode::fromString('require'),
        );

        $target = Rule\Target::create(
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
            Parser\Node\ArrayNode::class,
        );

        self::assertFalse($target->matches(
            $node,
            $path,
        ));
    }

    public function testMatchesReturnsTrueWhenNodeIsInstanceOfNodeClassAndPathSatisfiesLocation(): void
    {
        $node = Parser\Node\ArrayNode::create();
        $path = Parser\Traverser\Path::root()->property(
            Parser\Index::fromInt(0),
            Parser\Node\StringNode::fromString('bin'),
        );

        $target = Rule\Target::create(
            Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/bin')),
            Parser\Node\ArrayNode::class,
        );

        self::assertTrue($target->matches(
            $node,
            $path,
        ));
    }
}
