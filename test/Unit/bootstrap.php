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

use Symfony\Component\Filesystem;
use Symfony\Component\Finder;

require_once __DIR__ . '/../../vendor/autoload.php';

(static function (): void {
    $sourceDirectory = __DIR__ . '/../Template/Vendor/Composer/ComposerJsonNormalizer/NormalizeNormalizesJson/Json/IsObject/HasEntries/Yes/HasProperty/ValueContainsPackagesAndVersionConstraints';

    $targetDirectoryFrom = static function (string $directory): string {
        $testDirectory = __DIR__ . '/..';

        return \sprintf(
            '%s/Fixture/Vendor/Composer/ComposerJsonNormalizer/NormalizeNormalizesJson/Json/IsObject/HasEntries/Yes/HasProperty/%s',
            \realpath($testDirectory),
            $directory,
        );
    };

    $propertyToDirectory = [
        'conflict' => 'Conflict',
        'provide' => 'Provide',
        'replace' => 'Replace',
        'require' => 'Require',
        'require-dev' => 'RequireDev',
    ];

    $targetDirectories = \array_reduce(
        \array_values($propertyToDirectory),
        static function (array $targetDirectories, string $directory) use ($targetDirectoryFrom): array {
            $targetDirectory = $targetDirectoryFrom($directory);

            $realPathToTargetDirectory = \realpath($targetDirectory);

            if (!\is_string($realPathToTargetDirectory)) {
                return $targetDirectories;
            }

            if ('' === $realPathToTargetDirectory) {
                return $targetDirectories;
            }

            $targetDirectories[] = $realPathToTargetDirectory;

            return $targetDirectories;
        },
        [],
    );

    $filesystem = new Filesystem\Filesystem();

    if ([] !== $targetDirectories) {
        $filesystem->remove($targetDirectories);
    }

    $finder = Finder\Finder::create()
        ->files()
        ->in($sourceDirectory);

    $files = \iterator_to_array($finder);

    foreach ($propertyToDirectory as $property => $directory) {
        foreach ($files as $file) {
            $targetDirectory = $targetDirectoryFrom($directory);

            $targetFile = \sprintf(
                '%s/%s',
                $targetDirectory,
                $file->getRelativePathname(),
            );

            $directoryContainingTargetFile = \dirname($targetFile);

            if (!$filesystem->exists($directoryContainingTargetFile)) {
                $filesystem->mkdir($directoryContainingTargetFile);
            }

            $contents = $file->getContents();

            $filesystem->dumpFile(
                $targetFile,
                \str_replace(
                    '"value-contains-packages-and-version-constraints"',
                    \sprintf(
                        '"%s"',
                        $property,
                    ),
                    $contents,
                ),
            );
        }
    }
})();
