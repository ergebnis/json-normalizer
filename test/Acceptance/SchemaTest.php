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
use Ergebnis\Json\Normalizer\Runner;
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

/**
 * @coversNothing
 */
final class SchemaTest extends Framework\TestCase
{
    /**
     * @var list<string>
     */
    private const NOT_YET_PORTED = [
        'WithCustomJsonPointerSpecification/Json/IsObject/Schema/HasType/IsScalar/WithPropertyDefinitions',
        'WithCustomJsonPointerSpecification/Json/IsObject/Schema/HasType/IsScalar/WithPropertyDefinitionsAndAdditionalProperties',
        'WithCustomJsonPointerSpecification/Json/IsObject/Schema/HasType/IsScalar/WithoutPropertyDefinitions',
        'WithCustomJsonPointerSpecification/Json/IsObject/Schema/IsEmpty',
        'WithDefaultJsonPointerSpecification/Json/IsArray/Schema/HasAnyOf/Direct',
        'WithDefaultJsonPointerSpecification/Json/IsArray/Schema/HasAnyOf/HasReference',
        'WithDefaultJsonPointerSpecification/Json/IsArray/Schema/HasOneOf/Direct',
        'WithDefaultJsonPointerSpecification/Json/IsArray/Schema/HasOneOf/HasReference',
        'WithDefaultJsonPointerSpecification/Json/IsArray/Schema/HasReference',
        'WithDefaultJsonPointerSpecification/Json/IsArray/Schema/HasType/IsArray/WithItemDefinition/IsTuple',
        'WithDefaultJsonPointerSpecification/Json/IsArray/Schema/HasType/IsScalar/WithItemDefinition/IsTuple',
        'WithDefaultJsonPointerSpecification/Json/IsArray/Schema/IsEmpty',
        'WithDefaultJsonPointerSpecification/Json/IsObject/Schema/HasAnyOf',
        'WithDefaultJsonPointerSpecification/Json/IsObject/Schema/HasOneOf',
        'WithDefaultJsonPointerSpecification/Json/IsObject/Schema/HasReference',
        'WithDefaultJsonPointerSpecification/Json/IsObject/Schema/HasType/IsArray/WithPropertyDefinitions',
        'WithDefaultJsonPointerSpecification/Json/IsObject/Schema/HasType/IsArray/WithPropertyDefinitionsAndAdditionalProperties',
        'WithDefaultJsonPointerSpecification/Json/IsObject/Schema/HasType/IsArray/WithoutPropertyDefinitions',
        'WithDefaultJsonPointerSpecification/Json/IsObject/Schema/HasType/IsScalar/WithPropertyDefinitions',
        'WithDefaultJsonPointerSpecification/Json/IsObject/Schema/HasType/IsScalar/WithPropertyDefinitionsAndAdditionalProperties',
        'WithDefaultJsonPointerSpecification/Json/IsObject/Schema/HasType/IsScalar/WithoutPropertyDefinitions',
        'WithDefaultJsonPointerSpecification/Json/IsObject/Schema/IsEmpty',
    ];

    /**
     * @dataProvider provideCase
     */
    public function testNormalizeNormalizesInput(
        string $key,
        string $input,
        string $output
    ): void {
        $raw = Parser\Raw::fromString($input);

        $runner = Runner::create(self::configuration());

        if (\in_array($key, self::NOT_YET_PORTED, true)) {
            try {
                $result = $runner->normalize($raw);
            } catch (\Exception $exception) {
                $this->addToAssertionCount(1);

                return;
            }

            self::assertNotSame($output, $result->output()->toString(), \sprintf(
                'Case "%s" passes; remove it from NOT_YET_PORTED.',
                $key,
            ));

            return;
        }

        $result = $runner->normalize($raw);

        self::assertSame($output, $result->output()->toString());
    }

    /**
     * @return \Generator<string, array{0: string, 1: string, 2: string}>
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

            $cases[$key] = [
                $key,
                (string) \file_get_contents($fileInfo->getPathname()),
                (string) \file_get_contents(\sprintf(
                    '%s/output.json',
                    $fileInfo->getPath(),
                )),
            ];
        }

        \ksort($cases);

        yield from $cases;
    }

    private static function configuration(): Configuration
    {
        return Configuration::create();
    }
}
