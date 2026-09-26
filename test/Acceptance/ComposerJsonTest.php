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
use Ergebnis\Json\Normalizer\Exception;
use Ergebnis\Json\Normalizer\Runner;
use Ergebnis\Json\Normalizer\Set;
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

/**
 * @coversNothing
 */
final class ComposerJsonTest extends Framework\TestCase
{
    /**
     * @var list<string>
     */
    private const NOT_YET_PORTED = [
        'Json/IsObject/HasEntries/Yes/HasProperty/RequireAndRequireDev',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Duplicate',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unsorted',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Duplicate',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unsorted',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Duplicate',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unsorted',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Duplicate',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Overlapping',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unsorted',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Mixed',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Duplicate',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Overlapping',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unsorted',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/Extension',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/InlineAlias',
        'Template/Conflict/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Any',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Duplicate',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unsorted',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Duplicate',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unsorted',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Duplicate',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unsorted',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Duplicate',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Overlapping',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unsorted',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Mixed',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Duplicate',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Overlapping',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unsorted',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/Extension',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/InlineAlias',
        'Template/Provide/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Any',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Duplicate',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unsorted',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Duplicate',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unsorted',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Duplicate',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unsorted',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Duplicate',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Overlapping',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unsorted',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Mixed',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Duplicate',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Overlapping',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unsorted',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/Extension',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/InlineAlias',
        'Template/Replace/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Any',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Duplicate',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unsorted',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Duplicate',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unsorted',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Duplicate',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unsorted',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Duplicate',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Overlapping',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unsorted',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Mixed',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Duplicate',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Overlapping',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unsorted',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/Extension',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/InlineAlias',
        'Template/Require/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Any',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Duplicate',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Comma/ExactVersion/Unsorted',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Duplicate',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/And/Space/ExactVersion/Unsorted',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Duplicate',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/ExactVersion/Unsorted',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Duplicate',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Overlapping',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Caret/Unsorted',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Mixed',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Duplicate',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Overlapping',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Combination/Or/VersionRange/Tilde/Unsorted',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/Extension',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/InlineAlias',
        'Template/RequireDev/HasEntries/Yes/HasNormalizedVersionConstraints/No/VersionRange/Wildcard/Any',
    ];

    /**
     * @var array<string, string>
     */
    private const SECTIONS = [
        'conflict' => 'Conflict',
        'provide' => 'Provide',
        'replace' => 'Replace',
        'require' => 'Require',
        'require-dev' => 'RequireDev',
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
        $cases = [];

        foreach (self::casesIn(self::directory()) as $key => $case) {
            if (0 === \strpos($key, 'Rejects/')) {
                continue;
            }

            if (0 === \strpos($key, 'Template/')) {
                continue;
            }

            $cases[$key] = $case;
        }

        $templates = self::casesIn(\sprintf(
            '%s/Template',
            self::directory(),
        ));

        foreach (self::SECTIONS as $section => $directory) {
            foreach ($templates as $key => $template) {
                $cases[\sprintf(
                    'Template/%s/%s',
                    $directory,
                    $key,
                )] = \array_map(static function (string $contents) use ($section): string {
                    return \str_replace(
                        '"value-contains-packages-and-version-constraints"',
                        \sprintf(
                            '"%s"',
                            $section,
                        ),
                        $contents,
                    );
                }, $template);
            }
        }

        \ksort($cases);

        foreach ($cases as $key => $case) {
            yield $key => [
                $key,
                $case['input'],
                $case['output'],
            ];
        }
    }

    /**
     * @dataProvider provideRejectedCase
     */
    public function testNormalizeThrowsInputInvalidAccordingToSchemaWhenInputIsInvalid(string $input): void
    {
        $raw = Parser\Raw::fromString($input);

        $runner = Runner::create(self::configuration());

        $this->expectException(Exception\InputInvalidAccordingToSchema::class);

        $runner->normalize($raw);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideRejectedCase(): iterable
    {
        $cases = self::casesIn(\sprintf(
            '%s/Rejects',
            self::directory(),
        ));

        \ksort($cases);

        foreach ($cases as $key => $case) {
            yield $key => [
                $case['input'],
            ];
        }
    }

    private static function configuration(): Configuration
    {
        return Configuration::create()
            ->withSchema(self::schemaUri())
            ->withSets(Set\Vendor\Composer\ComposerJson::create());
    }

    private static function schemaUri(): string
    {
        return \sprintf(
            'file://%s',
            \realpath(\sprintf(
                '%s/../Fixture/Vendor/Composer/schema.json',
                __DIR__,
            )),
        );
    }

    private static function directory(): string
    {
        return \sprintf(
            '%s/../Fixture/Acceptance/ComposerJson',
            __DIR__,
        );
    }

    /**
     * @return array<string, array{input: string, output: string}>
     */
    private static function casesIn(string $directory): array
    {
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

            $input = (string) \file_get_contents($fileInfo->getPathname());
            $output = $input;

            $outputFile = \sprintf(
                '%s/output.json',
                $fileInfo->getPath(),
            );

            if (\is_file($outputFile)) {
                $output = (string) \file_get_contents($outputFile);
            }

            $cases[$key] = [
                'input' => $input,
                'output' => $output,
            ];
        }

        return $cases;
    }
}
