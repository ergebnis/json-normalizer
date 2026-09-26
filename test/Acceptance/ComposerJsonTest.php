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
use Ergebnis\Json\Normalizer\Normalizer;
use Ergebnis\Json\Normalizer\Set;
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

/**
 * @coversNothing
 */
final class ComposerJsonTest extends Framework\TestCase
{
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
        string $input,
        string $output
    ): void {
        $raw = Parser\Raw::fromString($input);

        $normalizer = Normalizer::create(self::configuration());

        $result = $normalizer->normalize($raw);

        self::assertSame($output, $result->output()->toString());
    }

    /**
     * @return \Generator<string, array{0: string, 1: string}>
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

        $normalizer = Normalizer::create(self::configuration());

        $this->expectException(Exception\InputInvalidAccordingToSchema::class);

        $normalizer->normalize($raw);
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
