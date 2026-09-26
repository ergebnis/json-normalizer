# json-normalizer

[![Integrate](https://github.com/ergebnis/json-normalizer/actions/workflows/integrate.yaml/badge.svg?branch=main)](https://github.com/ergebnis/json-normalizer/actions/workflows/integrate.yaml)
[![Merge](https://github.com/ergebnis/json-normalizer/actions/workflows/merge.yaml/badge.svg)](https://github.com/ergebnis/json-normalizer/actions/workflows/merge.yaml)
[![Release](https://github.com/ergebnis/json-normalizer/actions/workflows/release.yaml/badge.svg)](https://github.com/ergebnis/json-normalizer/actions/workflows/release.yaml)
[![Renew](https://github.com/ergebnis/json-normalizer/actions/workflows/renew.yaml/badge.svg)](https://github.com/ergebnis/json-normalizer/actions/workflows/renew.yaml)
[![Update](https://github.com/ergebnis/json-normalizer/actions/workflows/update.yaml/badge.svg)](https://github.com/ergebnis/json-normalizer/actions/workflows/update.yaml)

[![Code Coverage](https://codecov.io/gh/ergebnis/json-normalizer/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/json-normalizer)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/json-normalizer/v/stable)](https://packagist.org/packages/ergebnis/json-normalizer)
[![Total Downloads](https://poser.pugx.org/ergebnis/json-normalizer/downloads)](https://packagist.org/packages/ergebnis/json-normalizer)
[![Monthly Downloads](https://poser.pugx.org/ergebnis/json-normalizer/d/monthly)](https://packagist.org/packages/ergebnis/json-normalizer)

This project provides a [`composer`](https://getcomposer.org) package with a normalizer and generic and vendor-specific rules for normalizing [JSON documents](https://www.json.org).

## Installation

Run

```sh
composer require ergebnis/json-normalizer
```

## Usage

Create a `Configuration` with the rules or sets of rules you want to apply, create a `Normalizer`, and normalize a JSON document:

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Parser;
use Ergebnis\Json\Pointer;

$configuration = Normalizer\Configuration::create()
    ->withIndent(Parser\Indent::create(
        Parser\IndentSize::fromInt(4),
        Parser\IndentStyle::space(),
    ))
    ->withoutRules(Normalizer\Rule\Name::fromString('vendor/composer/version-constraint/replace-tilde-with-caret'))
    ->withSchema('https://getcomposer.org/schema.json')
    ->withSets(Normalizer\Set\Vendor\Composer\ComposerJson::create())
    ->withSkip(
        Normalizer\Rule\Name::fromString('sort/properties-by-name'),
        Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/extra')),
    );

$normalizer = Normalizer\Normalizer::create($configuration);

$file = \sprintf(
    '%s/composer.json',
    __DIR__,
);

$raw = Parser\Raw::fromString(\file_get_contents($file));

$result = $normalizer->normalize($raw);

if ($result->isChanged()) {
    foreach ($result->changes() as $change) {
        echo \sprintf(
            "Rule \"%s\" changed \"%s\".\n",
            $change->rule()->toString(),
            $change->path()->toJsonPointer()->toJsonString(),
        );
    }

    \file_put_contents(
        $file,
        $result->output()->toString(),
    );
}
```

The `Normalizer` keeps the format of the input (indentation, new lines, and a final new line) unless you configure a different one with `Configuration::withIndent()`, `Configuration::withNewLine()`, or `Configuration::withFinalNewLine()`.

When you configure a schema with `Configuration::withSchema()`, the `Normalizer` validates the input against the schema before applying rules and the output after applying them, and rules can use the schema of each node. The `Normalizer` throws `Exception\InputInvalidAccordingToSchema` when the input is not valid, and `Exception\OutputInvalidAccordingToSchema` when the rules produce output that is not valid.

The set `@composer-json` (`Set\Vendor\Composer\ComposerJson`) contains the rules for normalizing `composer.json` files. It expects the schema for `composer.json` (`https://getcomposer.org/schema.json`) to be configured: without a schema, the generic rules sort every object by name.

## Rules

<!-- BEGIN RULES -->

This project provides the following rules:

- [`prune/empty-optional-properties`](doc/rules/Prune/EmptyOptionalProperties.md): Removes properties that the schema lists but does not require when their value is an empty array, an empty object, or null.
- [`sort/properties-by-name`](doc/rules/Sort/PropertiesByName.md): Sorts the properties of objects that the schema does not list by name, after the properties that it lists.
- [`sort/properties-by-schema`](doc/rules/Sort/PropertiesBySchema.md): Sorts the properties of objects in the order in which the schema lists them, and keeps properties that the schema does not list after them, in their order.
- [`vendor/composer/bin/sort-elements`](doc/rules/Vendor/Composer/Bin/SortElements.md): Sorts the elements of `bin` by value.
- [`vendor/composer/config/sort-properties`](doc/rules/Vendor/Composer/Config/SortProperties.md): Sorts the properties of `config` by name.
- [`vendor/composer/config/sort-properties-with-wildcards`](doc/rules/Vendor/Composer/Config/SortPropertiesWithWildcards.md): Sorts the properties of `config.allow-plugins` and `config.preferred-install` by name, with a wildcard after every other character, unless a name has a wildcard other than at its end.
- [`vendor/composer/packages/merge-duplicate-extensions`](doc/rules/Vendor/Composer/Packages/MergeDuplicateExtensions.md): Renames extensions in package links to lower case with spaces replaced by hyphens, and merges the version constraints of extensions that then have the same name.
- [`vendor/composer/packages/sort-properties`](doc/rules/Vendor/Composer/Packages/SortProperties.md): Sorts package links with platform packages first, the way Composer sorts them.
- [`vendor/composer/repositories/sort-filter-elements`](doc/rules/Vendor/Composer/Repositories/SortFilterElements.md): Sorts the elements of `exclude` and `only` of repositories by value, with a wildcard after every other character, unless a value has a wildcard other than at its end.
- [`vendor/composer/version-constraint/move-dev-affix`](doc/rules/Vendor/Composer/VersionConstraint/MoveDevAffix.md): Moves `dev` to the end of numeric branch names and to the start of other branch names in version constraints.
- [`vendor/composer/version-constraint/normalize-separators`](doc/rules/Vendor/Composer/VersionConstraint/NormalizeSeparators.md): Separates or-constraints with `||` and and-constraints with a space.
- [`vendor/composer/version-constraint/remove-duplicates`](doc/rules/Vendor/Composer/VersionConstraint/RemoveDuplicates.md): Removes duplicate or-constraints and and-constraints from version constraints.
- [`vendor/composer/version-constraint/remove-extra-spaces`](doc/rules/Vendor/Composer/VersionConstraint/RemoveExtraSpaces.md): Replaces consecutive spaces in version constraints with a single space.
- [`vendor/composer/version-constraint/remove-leading-v`](doc/rules/Vendor/Composer/VersionConstraint/RemoveLeadingV.md): Removes the prefix `v` from versions in version constraints.
- [`vendor/composer/version-constraint/remove-overlapping`](doc/rules/Vendor/Composer/VersionConstraint/RemoveOverlapping.md): Removes or-constraints that other or-constraints with a caret or a tilde already cover from version constraints.
- [`vendor/composer/version-constraint/remove-useless-inline-aliases`](doc/rules/Vendor/Composer/VersionConstraint/RemoveUselessInlineAliases.md): Removes inline aliases that alias a version to itself from version constraints.
- [`vendor/composer/version-constraint/replace-tilde-with-caret`](doc/rules/Vendor/Composer/VersionConstraint/ReplaceTildeWithCaret.md): Replaces version ranges with a tilde with version ranges with a caret in version constraints where they are equivalent.
- [`vendor/composer/version-constraint/replace-wildcard-with-tilde`](doc/rules/Vendor/Composer/VersionConstraint/ReplaceWildcardWithTilde.md): Replaces version ranges with a wildcard with version ranges with a tilde in version constraints.
- [`vendor/composer/version-constraint/replace-x-with-asterisk`](doc/rules/Vendor/Composer/VersionConstraint/ReplaceXWithAsterisk.md): Replaces the wildcard `x` with `*` in version constraints.
- [`vendor/composer/version-constraint/sort`](doc/rules/Vendor/Composer/VersionConstraint/Sort.md): Sorts or-constraints and and-constraints in version constraints by version.
- [`vendor/composer/version-constraint/trim`](doc/rules/Vendor/Composer/VersionConstraint/Trim.md): Removes whitespace around version constraints.

<!-- END RULES -->

## Changelog

The maintainers of this project record notable changes to this project in a [changelog](CHANGELOG.md).

## Contributing

The maintainers of this project suggest following the [contribution guide](.github/CONTRIBUTING.md).

## Code of Conduct

The maintainers of this project ask contributors to follow the [code of conduct](https://github.com/ergebnis/.github/blob/main/CODE_OF_CONDUCT.md).

## General Support Policy

The maintainers of this project provide limited support.

You can support the maintenance of this project by [sponsoring @ergebnis](https://github.com/sponsors/ergebnis).

## PHP Version Support Policy

This project currently supports the following PHP versions:

- [PHP 7.4](https://www.php.net/releases/#7.4.0) (has reached its end of life on November 28, 2022)
- [PHP 8.0](https://www.php.net/releases/#8.0.0) (has reached its end of life on November 26, 2023)
- [PHP 8.1](https://www.php.net/releases/#8.1.0) (has reached its end of life on December 31, 2025)
- [PHP 8.2](https://www.php.net/releases/#8.2.0)
- [PHP 8.3](https://www.php.net/releases/#8.3.0)
- [PHP 8.4](https://www.php.net/releases/#8.4.0)
- [PHP 8.5](https://www.php.net/releases/#8.5.0)

The maintainers of this project add support for a PHP version following its initial release and _may_ drop support for a PHP version when it has reached its [end of life](https://www.php.net/supported-versions.php).

## Security Policy

This project has a [security policy](.github/SECURITY.md).

## License

This project uses the [MIT license](LICENSE.md).

## Credits

The algorithm for sorting packages in the [`Vendor\Composer\PackageHashNormalizer`](src/Vendor/Composer/PackageHashNormalizer.php) has been adopted from [`Composer\Json\JsonManipulator::sortPackages()`](https://github.com/composer/composer/blob/1.6.2/src/Composer/Json/JsonManipulator.php#L110-L146) (originally licensed under MIT by [Nils Adermann](https://github.com/naderman) and [Jordi Boggiano](https://github.com/seldaek)), which I initially contributed to `composer/composer` with [`composer/composer#3549`](https://github.com/composer/composer/pull/3549) and [`composer/composer#3872`](https://github.com/composer/composer/pull/3872).

## Social

Follow [@localheinz](https://twitter.com/intent/follow?screen_name=localheinz) and [@ergebnis](https://twitter.com/intent/follow?screen_name=ergebnis) on Twitter.
