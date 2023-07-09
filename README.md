# json-normalizer

[![Integrate](https://github.com/ergebnis/json-normalizer/workflows/Integrate/badge.svg)](https://github.com/ergebnis/json-normalizer/actions)
[![Merge](https://github.com/ergebnis/json-normalizer/workflows/Merge/badge.svg)](https://github.com/ergebnis/json-normalizer/actions)
[![Release](https://github.com/ergebnis/json-normalizer/workflows/Release/badge.svg)](https://github.com/ergebnis/json-normalizer/actions)
[![Renew](https://github.com/ergebnis/json-normalizer/workflows/Renew/badge.svg)](https://github.com/ergebnis/json-normalizer/actions)
[![Update](https://github.com/ergebnis/json-normalizer/workflows/Update/badge.svg)](https://github.com/ergebnis/json-normalizer/actions)

[![Code Coverage](https://codecov.io/gh/ergebnis/json-normalizer/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/json-normalizer)
[![Type Coverage](https://shepherd.dev/github/ergebnis/json-normalizer/coverage.svg)](https://shepherd.dev/github/ergebnis/json-normalizer)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/json-normalizer/v/stable)](https://packagist.org/packages/ergebnis/json-normalizer)
[![Total Downloads](https://poser.pugx.org/ergebnis/json-normalizer/downloads)](https://packagist.org/packages/ergebnis/json-normalizer)
[![Monthly Downloads](http://poser.pugx.org/ergebnis/json-normalizer/d/monthly)](https://packagist.org/packages/ergebnis/json-normalizer)

Provides generic and vendor-specific normalizers for normalizing JSON documents.

## Installation

Run

```sh
composer require ergebnis/json-normalizer
```

## Usage

This package comes with

- [generic normalizers](#generic-normalizers)
- [vendor-specific normalizers](#vendor-specific-normalizers)

### Generic normalizers

This package comes with the following generic normalizers:

- [`Ergebnis\Json\Normalizer\CallableNormalizer`](#callablenormalizer)
- [`Ergebnis\Json\Normalizer\ChainNormalizer`](#chainnormalizer)
- [`Ergebnis\Json\Normalizer\FormatNormalizer`](#formatnormalizer)
- [`Ergebnis\Json\Normalizer\IndentNormalizer`](#indentnormalizer)
- [`Ergebnis\Json\Normalizer\JsonEncodeNormalizer`](#jsonencodenormalizer)
- [`Ergebnis\Json\Normalizer\SchemaNormalizer`](#schemanormalizer)
- [`Ergebnis\Json\Normalizer\WithFinalNewLineNormalizer`](#withfinalnewlinenormalizer)
- [`Ergebnis\Json\Normalizer\WithoutFinalNewLineNormalizer`](#withoutfinalnewlinenormalizer)

:bulb: All of these normalizers implement the `Ergebnis\Json\Normalizer\Normalizer`.

#### `CallableNormalizer`

When you want to normalize a JSON file with a `callable`, you can use the `CallableNormalizer`.

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

$json = Json::fromString($encoded);

$callable = function (Json $json): Json {
    $decoded = $json->decoded();

    foreach (get_object_vars($decoded) as $name => $value) {
        if ('https://localheinz.com' !== $value) {
            continue;
        }

        $decoded->{$name} .= '/open-source/';
    }

    return Json::fromString(json_encode($decoded));
};

$normalizer = new Normalizer\CallableNormalizer($callable);

$normalized = $normalizer->normalize($json);
```

The normalized version will now have the callable applied to it.

#### `ChainNormalizer`

When you want to apply multiple normalizers in a chain, you can use the `ChainNormalizer`.

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Printer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

$json = Json::fromString($encoded);

$indent = Normalizer\Format\Indent::fromString('  ');
$jsonEncodeOptions = Normalizer\Format\JsonEncodeOptions::fromInt(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$normalizer = new Normalizer\ChainNormalizer(
    new Normalizer\JsonEncodeNormalizer($jsonEncodeOptions),
    new Normalizer\IndentNormalizer(
        $indent,
        new Printer\Printer()
    ),
    new Normalizer\WithFinalNewLineNormalizer()
);

$normalized = $normalizer->normalize($json);
```

The normalized version will now contain the result of applying all normalizers in a chain, one after another.

:bulb: Be careful with the order of the normalizers, as one normalizer might override changes a previous normalizer applied.

#### `FormatNormalizer`

When you want to normalize a JSON file with a formatting, you can use the `FormatNormalizer`.

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Printer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "emoji": "🤓",
    "url": "https://localheinz.com"
}
JSON;

$json = Json::fromString($encoded);

$format = Normalizer\Format\Format::create(
    Normalizer\Format\Indent::fromString('  '),
    Normalizer\Format\JsonEncodeOptions::fromInt(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    Normalizer\Format\NewLine::fromString("\r\n")
    true
);

$normalizer = new Normalizer\FormatNormalizer(
    new Printer\Printer(),
    $format,
);

$normalized = $normalizer->normalize($json);
```

The normalized version will now have formatting applied according to `$format`.

#### `IndentNormalizer`

When you need to adjust the indentation of a JSON file, you can use the `IndentNormalizer`.

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Printer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

$json = Json::fromString($encoded);

$indent = Normalizer\Format\Indent::fromString('  ');

$normalizer = new Normalizer\IndentNormalizer(
    $indent,
    new Printer\Printer()
);

$normalized = $normalizer->normalize($json);
```

The normalized version will now be indented with 2 spaces.

#### `JsonEncodeNormalizer`

When you need to adjust the encoding of a JSON file, you can use the `JsonEncodeNormalizer`.

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Json;
use Ergebnis\Json\Normalizer;

$encoded = <<<'JSON'
{
    "name": "Andreas M\u00f6ller",
    "url": "https:\/\/localheinz.com"
}
JSON;

$json = Json::fromString($encoded);

$jsonEncodeOptions = Normalizer\Format\JsonEncodeOptions::fromInt(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$normalizer = new Normalizer\JsonEncodeNormalizer($jsonEncodeOptions);

$normalized = $normalizer->normalize($json);
```

The normalized version will now be encoded with `$jsonEncodeOptions`.

:bulb: For reference, see [`json_encode()`](http://php.net/manual/en/function.json-encode.php) and the corresponding [JSON constants](http://php.net/manual/en/json.constants.php).

#### `SchemaNormalizer`

When you want to rebuild a JSON file according to a JSON schema, you can use the `SchemaNormalizer`.

Let's assume the following schema

```json
{
    "type": "object",
    "additionalProperties": true,
    "properties": {
        "name" : {
            "type" : "string"
        },
        "role" : {
            "type" : "string"
        }
    }
}
```

exists at `/schema/example.json`.

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Pointer;
use Ergebnis\Json\SchemaValidator;
use JsonSchema\SchemaStorage;

$encoded = <<<'JSON'
{
    "url": "https://localheinz.com",
    "name": "Andreas Möller",
    "open-source-projects": {
        "ergebnis/data-provider": {
            "downloads": {
                "total": 2,
                "monthly": 1
            }
        },
        "ergebnis/composer-normalize": {
            "downloads": {
                "total": 5,
                "monthly": 2
            }
        }
    }
}
JSON;

$json = Json::fromString($encoded);

$normalizer = new Normalizer\SchemaNormalizer(
    'file:///schema/example.json',
    new SchemaStorage(),
    new SchemaValidator\SchemaValidator(),
    Pointer\Specification::never()
);

$normalized = $normalizer->normalize($json);
```

The normalized version will now be structured according to the JSON schema (in this simple case, properties will be reordered as found in the schema and additional properties will be ordered by name). Internally, the `SchemaNormalizer` uses [`justinrainbow/json-schema`](https://github.com/justinrainbow/json-schema) to resolve schemas, as well as to ensure (before and after normalization) that the JSON document is valid.

If you have properties that you do not want to be reordered, you can use a `Pointer\Specification` to specify which properties should not be reordered.

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Pointer;
use Ergebnis\Json\SchemaValidator;
use JsonSchema\SchemaStorage;

$encoded = <<<'JSON'
{
    "url": "https://localheinz.com",
    "name": "Andreas Möller",
    "open-source-projects": {
        "ergebnis/data-provider": {
            "downloads": {
                "total": 2,
                "monthly": 1
            }
        },
        "ergebnis/composer-normalize": {
            "downloads": {
                "total": 5,
                "monthly": 2
            }
        }
    }
}
JSON;

$json = Json::fromString($encoded);

$normalizer = new Normalizer\SchemaNormalizer(
    'file:///schema/example.json',
    new SchemaStorage(),
    new SchemaValidator\SchemaValidator(),
    Pointer\Specification::equals(Pointer\JsonPointer::fromJsonString('/open-source-projects'))
);

$normalized = $normalizer->normalize($json);
```

:bulb: For more information about JSON schema, visit [json-schema.org](http://json-schema.org).

#### `WithFinalNewLineNormalizer`

When you want to ensure that a JSON file has a single final new line, you can use the `WithFinalNewLineNormalizer`.

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}


JSON;

$json = Json::fromString($encoded);

$normalizer = new Normalizer\WithFinalNewLineNormalizer();

$normalized = $normalizer->normalize($json);
```

The normalized version will now have a single final new line.

#### `WithoutFinalNewLineNormalizer`

When you want to ensure that a JSON file does not have a final new line, you can use the `WithoutFinalNewLineNormalizer`.

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}


JSON;

$json = Json::fromString($encoded);

$normalizer = new Normalizer\WithoutFinalNewLineNormalizer();

$normalized = $normalizer->normalize($json);
```

The normalized version will now not have a final new line or any whitespace at the end.

### Vendor-specific normalizers

This package comes with the following vendor-specific normalizers:

- [`Ergebnis\Json\Normalizer\Vendor\Composer\JsonNormalizer`](#vendorcomposercomposerjsonnormalizer)

#### `Vendor\Composer\ComposerJsonNormalizer`

The `Vendor\Composer\ComposerJsonNormalizer` can be used to normalize a `composer.json` file according to its underlying JSON schema.

It composes the following normalizers:

- [`Ergebnis\Composer\Json\Normalizer\Vendor\Composer\BinNormalizer`](#vendorcomposerbinnormalizer)
- [`Ergebnis\Composer\Json\Normalizer\Vendor\Composer\ConfigHashNormalizer`](#vendorcomposerconfighashnormalizer)
- [`Ergebnis\Composer\Json\Normalizer\Vendor\Composer\PackageHashNormalizer`](#vendorcomposerpackagehashnormalizer)
- [`Ergebnis\Composer\Json\Normalizer\Vendor\Composer\VersionConstraintNormalizer`](#vendorcomposerversionconstraintnormalizer)
- [`Ergebnis\Composer\Json\Normalizer\Vendor\WithFinalNewLineNormalizer`](#withfinalnewlinenormalizer)

#### `Vendor\Composer\BinNormalizer`

When `composer.json` contains an array of scripts in the `bin` section, the `Vendor\Composer\BinNormalizer` will sort the elements of the `bin` section by value in ascending order.

:bulb: Find out more about the `bin` section at [Composer: The composer.json schema](https://getcomposer.org/doc/04-schema.md#bin).

#### `Vendor\Composer\ConfigHashNormalizer`

When `composer.json` contains configuration in the `config` section, the `Vendor\Composer\ConfigHashNormalizer` will sort the content of these sections by key in ascending order.

:bulb: Find out more about the `config` section at [Composer: The composer.json schema](https://getcomposer.org/doc/06-config.md).

#### `Vendor\Composer\PackageHashNormalizer`

When `composer.json` contains any configuration in the

- `conflict`
- `provide`
- `replace`
- `require`
- `require-dev`
- `suggest`

sections, the `Vendor\Composer\PackageHashNormalizer` will sort the packages in these sections.

:bulb: This transfers the behaviour from using the `--sort-packages` or `sort-packages` configuration flag in `require` and `require-dev` to other sections. Find out more about the `--sort-packages` flag and configuration at [Composer: Config](https://getcomposer.org/doc/06-config.md#sort-packages) and [Composer: Command Line Interface / Commands](https://getcomposer.org/doc/03-cli.md#require).

#### `Vendor\Composer\VersionConstraintNormalizer`

When `composer.json` contains version constraints in the

- `conflict`
- `provide`
- `replace`
- `require`
- `require-dev`

sections, the `Vendor\Composer\VersionConstraintNormalizer` will ensure that

- all version constraints are trimmed

  ```diff
   {
     "homepage": "https://getcomposer.org/doc/articles/versions.md#version-range",
     "require": {
  -    "php": " ^8.2 "
  +    "php": "^8.2"
   }
  ```

- version constraints separated by a space (` `) or comma (`,`) - treated as a logical and - are separated by a space (` `) instead

  ```diff
   {
     "homepage": "https://getcomposer.org/doc/articles/versions.md#version-range",
     "require": {
  -    "foo/bar": "1.2.3,2.3.4",
  -    "foo/baz": "2.3.4   3.4.5"
  +    "foo/bar": "1.2.3 2.3.4",
  +    "foo/baz": "2.3.4 3.4.5"
   }
  ```

- version constraints separated by a single- (`|`) or double-pipe (`||`) and any number of spaces before and after - treated as a logical or - are separated by a double pipe with a single space before and after (` || `)

  ```diff
   {
     "homepage": "https://getcomposer.org/doc/articles/versions.md#version-range",
     "require": {
  -    "php": "^8.1|^8.2",
  -    "foo/bar": "^1.2.3  ||  ^2.3.4"
  +    "php": "^8.1 || ^8.2",
  +    "foo/bar": "^1.2.3 || ^2.3.4"
   }
  ```

- hyphenated version constraints separated by dash (` - `) and any positive number of spaces before and after are separated by a dash with a single space before and after (` - `)

  ```diff
   {
     "homepage": "https://getcomposer.org/doc/articles/versions.md#hyphenated-version-range-",
     "require": {
  -    "foo/bar": "1.2.3  -  2.3.4"
  +    "foo/bar": "1.2.3 - 2.3.4"
   }
  ```

- duplicate constraints are removed

  ```diff
   {
     "homepage": "https://getcomposer.org/doc/articles/versions.md#version-range",
     "require": {
  -    "foo/bar": "^1.0 || ^1.0 || ^2.0"
  +    "foo/bar": "^1.0 || ^2.0"
   }
  ```

- overlapping constraints are removed

  ```diff
   {
     "homepage": "https://getcomposer.org/doc/articles/versions.md#version-range",
     "require": {
  -    "foo/bar": "^1.0 || ^1.1 || ^2.0"
  +    "foo/bar": "^1.0 || ^2.0"
   }
  ```

- tilde operators (`~`) are preferred over wildcard (`*`) version ranges

  ```diff
   {
     "homepage": "https://getcomposer.org/doc/articles/versions.md#version-range",
     "require": {
       "foo/bar": "*",
  -    "foo/baz": "1.0.*"
  +    "foo/baz": "~1.0.0"
   }
  ```

- caret operators are preferred over tilde operators

  ```diff
   {
     "homepage": "https://getcomposer.org/doc/articles/versions.md#version-range",
     "require": {
  -    "foo/bar": "~1",
  -    "foo/baz": "~1.3"
  +    "foo/bar": "^1.0",
  +    "foo/baz": "^1.3"
   }
  ```

- version numbers are sorted in ascending order

  ```diff
   {
     "homepage": "https://getcomposer.org/doc/articles/versions.md#version-range",
     "require": {
  -    "foo/bar": "^2.0 || ^1.4"
  +    "foo/bar": "^1.4 || ^2.0"
   }
  ```

- extra spaces in inline aliases are removed

  ```diff
   {
     "homepage": "https://getcomposer.org/doc/articles/aliases.md#require-inline-alias",
     "require": {
  -    "foo/bar": "dev-2.x  as  2.0"
  +    "foo/bar": "dev-2.x as 2.0"
   }
  ```

- useless inline aliases are removed

  ```diff
   {
     "homepage": "https://getcomposer.org/doc/articles/aliases.md#require-inline-alias",
     "require": {
  -    "foo/bar": "2.0 as 2.0"
  +    "foo/bar": "2.0"
   }
  ```

:bulb: Find out more about version constraints at [Composer: Version and Constraints](https://getcomposer.org/doc/articles/versions.md).

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](https://github.com/ergebnis/.github/blob/main/CODE_OF_CONDUCT.md).

## Security Policy

Please have a look at [`SECURITY.md`](.github/SECURITY.md).

## License

This package is licensed using the MIT License.

Please have a look at [`LICENSE.md`](LICENSE.md).

## Credits

The algorithm for sorting packages in the [`Vendor\Composer\PackageHashNormalizer`](src/Vendor/Composer/PackageHashNormalizer.php) has been adopted from [`Composer\Json\JsonManipulator::sortPackages()`](https://github.com/composer/composer/blob/1.6.2/src/Composer/Json/JsonManipulator.php#L110-L146) (originally licensed under MIT by [Nils Adermann](https://github.com/naderman) and [Jordi Boggiano](https://github.com/seldaek)), which I initially contributed to `composer/composer` with [`composer/composer#3549`](https://github.com/composer/composer/pull/3549) and [`composer/composer#3872`](https://github.com/composer/composer/pull/3872).

## Curious what I am up to?

Follow me on [Twitter](https://twitter.com/intent/follow?screen_name=localheinz)!
