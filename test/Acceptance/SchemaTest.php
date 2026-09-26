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

namespace Ergebnis\Json\Normalizer\Test\Acceptance;

use Ergebnis\Json\Normalizer\Configuration;
use Ergebnis\Json\Normalizer\Normalizer;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;
use PHPUnit\Framework;

/**
 * @coversNothing
 */
final class SchemaTest extends Framework\TestCase
{
    /**
     * @dataProvider provideCase
     */
    public function testNormalizeNormalizesInput(
        string $input,
        string $output,
        string $schemaUri,
        ?Pointer\Specification $skip
    ): void {
        $raw = Parser\Raw::fromString($input);

        $normalizer = Normalizer::create(self::configuration(
            $schemaUri,
            $skip,
        ));

        $result = $normalizer->normalize($raw);

        self::assertSame($output, $result->output()->toString());
    }

    /**
     * @return \Generator<string, array{0: string, 1: string, 2: string, 3: null|Pointer\Specification}>
     */
    public static function provideCase(): iterable
    {
        $directory = \sprintf(
            '%s/../Fixture/Acceptance/Schema',
            __DIR__,
        );

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(
            $directory,
            \FilesystemIterator::SKIP_DOTS,
        ));

        $cases = [];

        foreach ($iterator as $fileInfo) {
            /** @var \SplFileInfo $fileInfo */
            if ('input.json' !== $fileInfo->getBasename()) {
                continue;
            }

            $key = \substr(
                $fileInfo->getPath(),
                \strlen($directory) + 1,
            );

            $skip = null;

            $skipFile = \sprintf(
                '%s/skip.php',
                $fileInfo->getPath(),
            );

            if (\is_file($skipFile)) {
                /** @var Pointer\Specification $skip */
                $skip = include $skipFile;
            }

            $cases[$key] = [
                (string) \file_get_contents($fileInfo->getPathname()),
                (string) \file_get_contents(\sprintf(
                    '%s/output.json',
                    $fileInfo->getPath(),
                )),
                \sprintf(
                    'file://%s/schema.json',
                    $fileInfo->getPath(),
                ),
                $skip,
            ];
        }

        \ksort($cases);

        yield from $cases;
    }

    private static function configuration(
        string $schemaUri,
        ?Pointer\Specification $skip
    ): Configuration {
        $rules = [
            Rule\Sort\PropertiesBySchema::create(),
            Rule\Sort\PropertiesByName::create(),
        ];

        $configuration = Configuration::create()
            ->withRules(...$rules)
            ->withSchema($schemaUri);

        if (!$skip instanceof Pointer\Specification) {
            return $configuration;
        }

        foreach ($rules as $rule) {
            $configuration = $configuration->withSkip(
                $rule->name(),
                $skip,
            );
        }

        return $configuration;
    }
}
