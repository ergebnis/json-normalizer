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

namespace Ergebnis\Json\Normalizer\Test\Unit\Set;

use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Set;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Json\Normalizer\Set\Name
 *
 * @uses \Ergebnis\Json\Normalizer\Exception\InvalidSetName
 */
final class NameTest extends Framework\TestCase
{
    /**
     * @dataProvider provideValueThatIsNotAtSignFollowedByKebabCaseSegments
     */
    public function testFromStringThrowsInvalidSetNameWhenValueIsNotAtSignFollowedByKebabCaseSegments(string $value): void
    {
        $this->expectException(Exception\InvalidSetName::class);

        Set\Name::fromString($value);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideValueThatIsNotAtSignFollowedByKebabCaseSegments(): iterable
    {
        $values = [
            'empty' => '',
            'at-sign-only' => '@',
            'without-at-sign' => 'composer-json',
            'upper-case' => '@Composer-json',
            'trailing-slash' => '@vendor/composer/',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    /**
     * @dataProvider provideValueThatIsAtSignFollowedByKebabCaseSegments
     */
    public function testFromStringReturnsNameWhenValueIsAtSignFollowedByKebabCaseSegments(string $value): void
    {
        $name = Set\Name::fromString($value);

        self::assertSame($value, $name->toString());
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideValueThatIsAtSignFollowedByKebabCaseSegments(): iterable
    {
        $values = [
            'one-segment' => '@composer-json',
            'two-segments' => '@vendor/composer',
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }
}
