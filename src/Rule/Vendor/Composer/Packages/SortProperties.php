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

namespace Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Packages;

use Ergebnis\Json\Normalizer\Context;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;

final class SortProperties implements Rule
{
    /**
     * @see https://github.com/composer/composer/blob/2.0.11/src/Composer/Repository/PlatformRepository.php#L33
     */
    private const PLATFORM_PACKAGE_REGEX = '{^(?:php(?:-64bit|-ipv6|-zts|-debug)?|hhvm|(?:ext|lib)-[a-z0-9](?:[_.-]?[a-z0-9]+)*|composer-(?:plugin|runtime)-api)$}iD';

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function name(): Rule\Name
    {
        return Rule\Name::fromString('vendor/composer/packages/sort-properties');
    }

    public function target(): Rule\Target
    {
        return Rule\Target::create(
            Pointer\Specification::anyOf(
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/conflict')),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/provide')),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/replace')),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/require')),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/require-dev')),
                Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/suggest')),
            ),
            Parser\Node\ObjectNode::class,
        );
    }

    public function definition(): Rule\Definition
    {
        return Rule\Definition::create(
            'Sorts package links with platform packages first, the way Composer sorts them.',
            Rule\Example::create(
                <<<'JSON'
{
    "require": {
        "ergebnis/json": "^1.0",
        "ext-json": "*",
        "php": "^8.0"
    }
}

JSON,
                <<<'JSON'
{
    "require": {
        "php": "^8.0",
        "ext-json": "*",
        "ergebnis/json": "^1.0"
    }
}

JSON,
            ),
        );
    }

    /**
     * This code is adopted from composer/composer (originally licensed under MIT by Nils Adermann <naderman@naderman.de>
     * and Jordi Boggiano <j.boggiano@seld.be>).
     *
     * @see https://github.com/composer/composer/blob/1.6.2/src/Composer/Json/JsonManipulator.php#L110-L146
     */
    public function apply(
        Parser\Node\Node $node,
        Context $context
    ): Rule\Action {
        if (!$node instanceof Parser\Node\ObjectNode) {
            return Rule\Action::keep();
        }

        $properties = $node->properties();
        $prefixed = [];

        foreach ($properties as $index => $property) {
            $prefixed[$index] = self::prefix($property->name()->toString());
        }

        $indexes = \array_keys($properties);

        \usort($indexes, static function (int $one, int $two) use ($prefixed): int {
            $comparison = \strnatcmp(
                $prefixed[$one],
                $prefixed[$two],
            );

            if (0 !== $comparison) {
                return $comparison;
            }

            return $one <=> $two;
        });

        if (\array_keys($properties) === $indexes) {
            return Rule\Action::keep();
        }

        $sorted = [];

        foreach ($indexes as $index) {
            $sorted[] = $properties[$index];
        }

        return Rule\Action::replace(Parser\Node\ObjectNode::create(...$sorted));
    }

    private static function prefix(string $name): string
    {
        if (1 === \preg_match(self::PLATFORM_PACKAGE_REGEX, $name)) {
            return (string) \preg_replace(
                [
                    '/^php/',
                    '/^hhvm/',
                    '/^ext/',
                    '/^lib/',
                    '/^\D/',
                ],
                [
                    '0-$0',
                    '1-$0',
                    '2-$0',
                    '3-$0',
                    '4-$0',
                ],
                $name,
            );
        }

        return \sprintf(
            '5-%s',
            $name,
        );
    }
}
