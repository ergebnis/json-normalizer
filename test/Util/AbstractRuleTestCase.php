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

namespace Ergebnis\Json\Normalizer\Test\Util;

use Ergebnis\Json\Normalizer\Configuration;
use Ergebnis\Json\Normalizer\Rule;
use Ergebnis\Json\Normalizer\Runner;
use Ergebnis\Json\Parser;
use PHPUnit\Framework;

abstract class AbstractRuleTestCase extends Framework\TestCase
{
    final public function testNameMirrorsNamespaceBelowRule(): void
    {
        $rule = static::rule();

        self::assertSame(self::nameFromClass(\get_class($rule)), $rule->name()->toString());
    }

    /**
     * @dataProvider provideCase
     */
    final public function testApplyNormalizesInput(
        string $input,
        string $output
    ): void {
        $raw = Parser\Raw::fromString($input);

        $result = self::runner()->normalize($raw);

        self::assertSame($output, $result->output()->toString());

        if ($input === $output) {
            self::assertSame([], $result->changes());
        }
    }

    /**
     * @return \Generator<string, array{0: string, 1: string}>
     */
    final public static function provideCase(): iterable
    {
        foreach (self::cases() as $case => $files) {
            yield $case => [
                $files['input'],
                $files['output'],
            ];
        }
    }

    /**
     * @dataProvider provideCaseWithOutput
     */
    final public function testApplyKeepsOutput(string $output): void
    {
        $raw = Parser\Raw::fromString($output);

        $result = self::runner()->normalize($raw);

        self::assertSame($output, $result->output()->toString());
        self::assertSame([], $result->changes());
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    final public static function provideCaseWithOutput(): iterable
    {
        foreach (self::cases() as $case => $files) {
            if ($files['input'] === $files['output']) {
                continue;
            }

            yield $case => [
                $files['output'],
            ];
        }
    }

    final public function testDefinitionExamplesAreNormalized(): void
    {
        foreach (static::rule()->definition()->examples() as $example) {
            $raw = Parser\Raw::fromString($example->input());

            $result = self::runner()->normalize($raw);

            self::assertSame($example->output(), $result->output()->toString());
        }
    }

    abstract protected static function rule(): Rule;

    private static function runner(): Runner
    {
        return Runner::create(Configuration::create()->withRules(KeepVerifyingRule::create(static::rule())));
    }

    /**
     * @return array<string, array{input: string, output: string}>
     */
    private static function cases(): array
    {
        $directory = \sprintf(
            '%s/../Fixture/Rule/%s',
            __DIR__,
            \str_replace(
                '\\',
                '/',
                self::namespaceBelowRule(\get_class(static::rule())),
            ),
        );

        $inputFiles = \glob(\sprintf(
            '%s/*/input.json',
            $directory,
        ));

        if (!\is_array($inputFiles)) {
            return [];
        }

        $cases = [];

        foreach ($inputFiles as $inputFile) {
            $caseDirectory = \dirname($inputFile);

            $input = (string) \file_get_contents($inputFile);
            $output = $input;

            $outputFile = \sprintf(
                '%s/output.json',
                $caseDirectory,
            );

            if (\is_file($outputFile)) {
                $output = (string) \file_get_contents($outputFile);
            }

            $cases[\basename($caseDirectory)] = [
                'input' => $input,
                'output' => $output,
            ];
        }

        return $cases;
    }

    private static function namespaceBelowRule(string $className): string
    {
        $prefix = 'Ergebnis\\Json\\Normalizer\\Rule\\';

        return \substr(
            $className,
            \strlen($prefix),
        );
    }

    private static function nameFromClass(string $className): string
    {
        $segments = \explode(
            '\\',
            self::namespaceBelowRule($className),
        );

        $kebabCaseSegments = \array_map(static function (string $segment): string {
            $kebabCaseSegment = \preg_replace(
                '/(?<!^)[A-Z]/',
                '-$0',
                $segment,
            );

            return \strtolower((string) $kebabCaseSegment);
        }, $segments);

        return \implode(
            '/',
            $kebabCaseSegments,
        );
    }
}
