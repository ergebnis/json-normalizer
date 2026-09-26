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

use Ergebnis\Json\Normalizer\Rule;
use SebastianBergmann\Diff;

require_once __DIR__ . '/../vendor/autoload.php';

(static function (): void {
    $namespacePrefix = 'Ergebnis\\Json\\Normalizer\\';
    $rootDirectory = \dirname(__DIR__);
    $sourceDirectory = \sprintf(
        '%s/src',
        $rootDirectory,
    );
    $ruleDirectory = \sprintf(
        '%s/Rule',
        $sourceDirectory,
    );
    $documentationDirectory = \sprintf(
        '%s/doc/rules',
        $rootDirectory,
    );
    $readmeFile = \sprintf(
        '%s/README.md',
        $rootDirectory,
    );

    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(
        $ruleDirectory,
        \FilesystemIterator::SKIP_DOTS,
    ));

    $rules = [];

    foreach ($iterator as $fileInfo) {
        /** @var \SplFileInfo $fileInfo */
        if ('php' !== $fileInfo->getExtension()) {
            continue;
        }

        $className = \sprintf(
            '%s%s',
            $namespacePrefix,
            \str_replace(
                '/',
                '\\',
                \substr(
                    $fileInfo->getPathname(),
                    \strlen($sourceDirectory) + 1,
                    -\strlen('.php'),
                ),
            ),
        );

        if (!\class_exists($className)) {
            continue;
        }

        $reflectionClass = new \ReflectionClass($className);

        if (
            $reflectionClass->isAbstract()
            || !$reflectionClass->implementsInterface(Rule::class)
        ) {
            continue;
        }

        $rule = $className::create();

        if (!$rule instanceof Rule) {
            continue;
        }

        $rules[$rule->name()->toString()] = $rule;
    }

    \ksort($rules);

    $differ = new Diff\Differ(new Diff\Output\DiffOnlyOutputBuilder(''));

    $diff = static function (
        string $before,
        string $after
    ) use ($differ): string {
        $entries = $differ->diffToArray(
            $before,
            $after,
        );

        $lines = [];

        foreach ($entries as $entry) {
            $line = \rtrim(
                $entry[0],
                "\n",
            );

            if (Diff\Differ::REMOVED === $entry[1]) {
                $lines[] = \sprintf(
                    '-%s',
                    $line,
                );

                continue;
            }

            if (Diff\Differ::ADDED === $entry[1]) {
                $lines[] = \sprintf(
                    '+%s',
                    $line,
                );

                continue;
            }

            if (Diff\Differ::OLD === $entry[1]) {
                $lines[] = \sprintf(
                    ' %s',
                    $line,
                );
            }
        }

        return \implode(
            "\n",
            $lines,
        );
    };

    $pathOf = static function (Rule $rule) use ($namespacePrefix): string {
        return \sprintf(
            'doc/rules/%s.md',
            \str_replace(
                '\\',
                '/',
                \substr(
                    \get_class($rule),
                    \strlen(\sprintf(
                        '%sRule\\',
                        $namespacePrefix,
                    )),
                ),
            ),
        );
    };

    $removeDirectory = static function (string $directory) use (&$removeDirectory): void {
        if (!\is_dir($directory)) {
            return;
        }

        $entries = \scandir($directory);

        if (!\is_array($entries)) {
            return;
        }

        foreach ($entries as $entry) {
            if (
                '.' === $entry
                || '..' === $entry
            ) {
                continue;
            }

            $path = \sprintf(
                '%s/%s',
                $directory,
                $entry,
            );

            if (\is_dir($path)) {
                $removeDirectory($path);

                continue;
            }

            \unlink($path);
        }

        \rmdir($directory);
    };

    $removeDirectory($documentationDirectory);

    foreach ($rules as $name => $rule) {
        $definition = $rule->definition();

        $lines = [
            \sprintf(
                '# `%s`',
                $name,
            ),
            '',
            $definition->description(),
            '',
            \sprintf(
                'Class: `%s`',
                \get_class($rule),
            ),
            '',
            '## Examples',
            '',
        ];

        $className = \sprintf(
            'Normalizer\\%s',
            \substr(
                \get_class($rule),
                \strlen($namespacePrefix),
            ),
        );

        foreach ($definition->examples() as $index => $example) {
            $lines[] = \sprintf(
                '### Example %d',
                $index + 1,
            );
            $lines[] = '';
            $lines[] = '#### Configuration';
            $lines[] = '';
            $lines[] = '```php';
            $lines[] = '<?php';
            $lines[] = '';
            $lines[] = 'declare(strict_types=1);';
            $lines[] = '';
            $lines[] = 'use Ergebnis\\Json\\Normalizer;';
            $lines[] = '';
            $lines[] = \sprintf(
                '$configuration = Normalizer\\Configuration::create()->withRules(%s::create());',
                $className,
            );

            $lines[] = '```';
            $lines[] = '';

            $lines[] = '#### Changes';
            $lines[] = '';
            $lines[] = '```diff';
            $lines[] = $diff(
                $example->input(),
                $example->output(),
            );
            $lines[] = '```';
            $lines[] = '';
        }

        $file = \sprintf(
            '%s/%s',
            $rootDirectory,
            $pathOf($rule),
        );

        $directory = \dirname($file);

        if (!\is_dir($directory)) {
            \mkdir(
                $directory,
                0755,
                true,
            );
        }

        \file_put_contents(
            $file,
            \implode(
                "\n",
                $lines,
            ),
        );

        echo \sprintf(
            "Generated %s\n",
            $pathOf($rule),
        );
    }

    $readme = \file_get_contents($readmeFile);

    if (!\is_string($readme)) {
        throw new \RuntimeException(\sprintf(
            'Could not read "%s".',
            $readmeFile,
        ));
    }

    $beginMarker = '<!-- BEGIN RULES -->';
    $endMarker = '<!-- END RULES -->';

    $beginPosition = \strpos(
        $readme,
        $beginMarker,
    );

    $endPosition = \strpos(
        $readme,
        $endMarker,
    );

    if (
        false === $beginPosition
        || false === $endPosition
    ) {
        throw new \RuntimeException(\sprintf(
            'Could not find markers "%s" and "%s" in "%s".',
            $beginMarker,
            $endMarker,
            $readmeFile,
        ));
    }

    $list = [
        'This project provides the following rules:',
        '',
    ];

    foreach ($rules as $name => $rule) {
        $list[] = \sprintf(
            '- [`%s`](%s): %s',
            $name,
            $pathOf($rule),
            $rule->definition()->description(),
        );
    }

    \file_put_contents(
        $readmeFile,
        \sprintf(
            "%s%s\n\n%s\n\n%s",
            \substr(
                $readme,
                0,
                $beginPosition,
            ),
            $beginMarker,
            \implode(
                "\n",
                $list,
            ),
            \substr(
                $readme,
                $endPosition,
            ),
        ),
    );

    echo "Updated README.md\n";
})();
