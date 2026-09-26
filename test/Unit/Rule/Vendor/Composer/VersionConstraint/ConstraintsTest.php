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

namespace Ergebnis\Json\Normalizer\Test\Unit\Rule\Vendor\Composer\VersionConstraint;

use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\Constraints
 *
 * @uses \Ergebnis\Json\Normalizer\Rule\Target
 */
final class ConstraintsTest extends Framework\TestCase
{
    /**
     * @dataProvider providePathThatIsNotVersionConstraintOfPackageLink
     */
    public function testTargetDoesNotMatchStringNodeWhenPathIsNotVersionConstraintOfPackageLink(Parser\Traverser\Path $path): void
    {
        $target = Rule\Vendor\Composer\VersionConstraint\Constraints::target();

        $matches = $target->matches(
            Parser\Node\StringNode::fromString('^1.0'),
            $path,
        );

        self::assertFalse($matches);
    }

    /**
     * @return \Generator<string, array{0: Parser\Traverser\Path}>
     */
    public static function providePathThatIsNotVersionConstraintOfPackageLink(): iterable
    {
        yield 'suggest' => [
            self::path(
                'suggest',
                'vendor/package',
            ),
        ];

        yield 'extra' => [
            self::path(
                'extra',
                'require',
            ),
        ];

        yield 'section' => [
            Parser\Traverser\Path::root()->property(
                Parser\Index::fromInt(0),
                Parser\Node\StringNode::fromString('require'),
            ),
        ];

        yield 'below-version-constraint' => [
            self::path(
                'require',
                'vendor/package',
            )->element(Parser\Index::fromInt(0)),
        ];
    }

    /**
     * @dataProvider provideSection
     */
    public function testTargetMatchesStringNodeWhenPathIsVersionConstraintOfPackageLink(string $section): void
    {
        $target = Rule\Vendor\Composer\VersionConstraint\Constraints::target();

        $path = self::path(
            $section,
            'vendor/package',
        );

        $expected = \sprintf(
            '/%s/vendor~1package',
            $section,
        );

        self::assertSame($expected, $path->toJsonPointer()->toJsonString());
        $matchesString = $target->matches(
            Parser\Node\StringNode::fromString('^1.0'),
            $path,
        );

        $matchesNumber = $target->matches(
            Parser\Node\NumberNode::fromInt(1),
            $path,
        );

        self::assertTrue($matchesString);
        self::assertFalse($matchesNumber);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideSection(): iterable
    {
        $sections = [
            'conflict',
            'provide',
            'replace',
            'require',
            'require-dev',
        ];

        foreach ($sections as $section) {
            yield $section => [
                $section,
            ];
        }
    }

    /**
     * @dataProvider provideUnparsableVersionConstraint
     */
    public function testIsParsableReturnsFalseWhenVersionConstraintIsNotParsable(string $versionConstraint): void
    {
        self::assertFalse(Rule\Vendor\Composer\VersionConstraint\Constraints::isParsable($versionConstraint));
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideUnparsableVersionConstraint(): iterable
    {
        $values = [
            'empty' => '',
            'double-comma' => '>2.0,,<=3.0',
            'caret-only' => '^',
            'unknown-stability' => '1.0.0-meh',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    public function testIsParsableReturnsTrueWhenVersionConstraintIsParsable(): void
    {
        self::assertTrue(Rule\Vendor\Composer\VersionConstraint\Constraints::isParsable('^1.0 || ~2.3.4'));
    }

    public function testSplitIntoOrConstraintsReturnsOrConstraints(): void
    {
        $orConstraints = Rule\Vendor\Composer\VersionConstraint\Constraints::splitIntoOrConstraints('^1.0|^2.0 || >=3.0 <3.5');

        $expected = [
            '^1.0',
            '^2.0',
            '>=3.0 <3.5',
        ];

        self::assertSame($expected, $orConstraints);
    }

    public function testJoinOrConstraintsReturnsOrConstraintsJoinedWithDoublePipe(): void
    {
        $versionConstraint = Rule\Vendor\Composer\VersionConstraint\Constraints::joinOrConstraints(
            '^1.0',
            '^2.0',
        );

        self::assertSame('^1.0 || ^2.0', $versionConstraint);
    }

    public function testSplitIntoAndConstraintsReturnsAndConstraints(): void
    {
        $andConstraints = Rule\Vendor\Composer\VersionConstraint\Constraints::splitIntoAndConstraints('>=1.0, <2.0 !=1.5');

        $expected = [
            '>=1.0',
            '<2.0',
            '!=1.5',
        ];

        self::assertSame($expected, $andConstraints);
    }

    public function testJoinAndConstraintsReturnsAndConstraintsJoinedWithSpace(): void
    {
        $versionConstraint = Rule\Vendor\Composer\VersionConstraint\Constraints::joinAndConstraints(
            '>=1.0',
            '<2.0',
        );

        self::assertSame('>=1.0 <2.0', $versionConstraint);
    }

    public function testApplyToVersionsInTurnReplacesInEveryVersion(): void
    {
        $versionConstraint = Rule\Vendor\Composer\VersionConstraint\Constraints::applyToVersionsInTurn(
            'v1.0 || >=v2.0 <v3.0',
            '{^(|[<>]=?)v}',
            '$1',
        );

        self::assertSame('1.0 || >=2.0 <3.0', $versionConstraint);
    }

    private static function path(
        string $section,
        string $name
    ): Parser\Traverser\Path {
        return Parser\Traverser\Path::root()
            ->property(
                Parser\Index::fromInt(0),
                Parser\Node\StringNode::fromString($section),
            )
            ->property(
                Parser\Index::fromInt(0),
                Parser\Node\StringNode::fromString($name),
            );
    }
}
