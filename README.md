# json-normalizer

[![Integrate](https://github.com/ergebnis/json-normalizer/workflows/Integrate/badge.svg)](https://github.com/ergebnis/json-normalizer/actions)
[![Prune](https://github.com/ergebnis/json-normalizer/workflows/Prune/badge.svg)](https://github.com/ergebnis/json-normalizer/actions)
[![Release](https://github.com/ergebnis/json-normalizer/workflows/Release/badge.svg)](https://github.com/ergebnis/json-normalizer/actions)
[![Renew](https://github.com/ergebnis/json-normalizer/workflows/Renew/badge.svg)](https://github.com/ergebnis/json-normalizer/actions)

[![Code Coverage](https://codecov.io/gh/ergebnis/json-normalizer/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/json-normalizer)
[![Type Coverage](https://shepherd.dev/github/ergebnis/json-normalizer/coverage.svg)](https://shepherd.dev/github/ergebnis/json-normalizer)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/json-normalizer/v/stable)](https://packagist.org/packages/ergebnis/json-normalizer)
[![Total Downloads](https://poser.pugx.org/ergebnis/json-normalizer/downloads)](https://packagist.org/packages/ergebnis/json-normalizer)

Provides generic and vendor-specific normalizers for normalizing JSON documents.

## Installation

Run

```
$ composer require ergebnis/json-normalizer
```

## Usage

### Generic normalizers

This package comes with the following generic normalizers:

* [`Ergebnis\Json\Normalizer\AutoFormatNormalizer`](#autoformatnormalizer)
* [`Ergebnis\Json\Normalizer\CallableNormalizer`](#callablenormalizer)
* [`Ergebnis\Json\Normalizer\ChainNormalizer`](#chainnormalizer)
* [`Ergebnis\Json\Normalizer\FinalNewLineNormalizer`](#finalnewlinenormalizer)
* [`Ergebnis\Json\Normalizer\FixedFormatNormalizer`](#fixedformatnormalizer)
* [`Ergebnis\Json\Normalizer\IndentNormalizer`](#indentnormalizer)
* [`Ergebnis\Json\Normalizer\JsonEncodeNormalizer`](#jsonencodenormalizer)
* [`Ergebnis\Json\Normalizer\NoFinalNewLineNormalizer`](#nofinalnewlinenormalizer)
* [`Ergebnis\Json\Normalizer\SchemaNormalizer`](#schemanormalizer)

:bulb: All of these normalizers implement the `Ergebnis\Json\Normalizer\NormalizerInterface`.

#### `AutoFormatNormalizer`

When you want to normalize a JSON file with an implementation of `NormalizerInterface`, but retain the original formatting, you can use the `AutoFormatNormalizer`.

```php
<?php

use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Printer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

$json = Normalizer\Json::fromEncoded($encoded);

/** @var Normalizer\NormalizerInterface $composedNormalizer*/
$normalizer = new Normalizer\AutoFormatNormalizer(
    $composedNormalizer,
    new Normalizer\Format\Formatter(new Printer\Printer())
);

$normalized = $normalizer->normalize($json);
```

The normalized version will now have the composed normalizer applied, but also retained the original formatting (within certain limits). Before applying the composer normalizer, the `AutoFormatNormalizer` will attempt to detect the following:

* `json_encode()` options
* indent
* whether a final new line exists or not

After applying the composed normalizer, the `AutoFormatNormalizer` will

* decode with `json_decode()` and encode again with `json_encode()`, passing in the previously detected options
* indent with the detected indent
* add a final new line of detected

:bulb: Alternatively, you can use the [`FixedFormatNormalizer`](#fixedformatnormalizer).

#### `CallableNormalizer`

When you want to normalize a JSON file with a `callable`, you can use the `CallableNormalizer`.

```php
<?php

use Ergebnis\Json\Normalizer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

$json = Normalizer\Json::fromEncoded($encoded);

$callable = function (Normalizer\Json $json): Normalizer\Json {
    $decoded = $json->decoded();

    foreach (get_object_vars($decoded) as $name => $value) {
        if ('https://localheinz.com' !== $value) {
            continue;
        }

        $decoded->{$name} .= '/open-source/';
    }

    return Normalizer\Json::fromEncoded(json_encode($decoded));
};

$normalizer = new Normalizer\CallableNormalizer($callable);

$normalized = $normalizer->normalize($json);
```

The normalized version will now have the callable applied to it.

#### `ChainNormalizer`

When you want to apply multiple normalizers in a chain, you can use the `ChainNormalizer`.

```php
<?php

use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Printer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

$json = Normalizer\Json::fromEncoded($encoded);

$indent = Normalizer\Format\Indent::fromString('  ');
$jsonEncodeOptions = Normalizer\Format\JsonEncodeOptions::fromInt(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$normalizer = new Normalizer\ChainNormalizer(
    new Normalizer\JsonEncodeNormalizer($jsonEncodeOptions),
    new Normalizer\IndentNormalizer(
        $indent,
        new Printer\Printer()
    ),
    new Normalizer\FinalNewLineNormalizer()
);

$normalized = $normalizer->normalize($json);
```

The normalized version will now contain the result of applying all normalizers in a chain, one after another.

:bulb: Be careful with the order of the normalizers, as one normalizer might override changes a previous normalizer applied.

#### `FinalNewLineNormalizer`

When you want to ensure that a JSON file has a single final new line, you can use the `FinalNewLineNormalizer`.

```php
<?php

use Ergebnis\Json\Normalizer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}


JSON;

$json = Normalizer\Json::fromEncoded($encoded);

$normalizer = new Normalizer\FinalNewLineNormalizer();

$normalized = $normalizer->normalize($json);
```

The normalized version will now have a single final new line.

#### `FixedFormatNormalizer`

When you want to normalize a JSON file with an implementation of `NormalizerInterface`, but apply a fixed formatting, you can use the `FixedFormatNormalizer`.

```php
<?php

use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Printer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

$json = Normalizer\Json::fromEncoded($encoded);

/** @var Normalizer\NormalizerInterface $composedNormalizer*/
/** @var Normalizer\Format\Format $format*/
$normalizer = new Normalizer\FixedFormatNormalizer(
    $composedNormalizer,
    $format,
    new Normalizer\Format\Formatter(new Printer\Printer())
);

$normalized = $normalizer->normalize($json);
```

The normalized version will now have the composed normalizer applied, but also the formatting applied according to `$format`.

:bulb: Alternatively, you can use the [`AutoFormatNormalizer`](#autoformatnormalizer).

#### `IndentNormalizer`

When you need to adjust the indentation of a JSON file, you can use the `IndentNormalizer`.

```php
<?php

use Ergebnis\Json\Normalizer;
use Ergebnis\Json\Printer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

$json = Normalizer\Json::fromEncoded($encoded);

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

use Ergebnis\Json\Normalizer;

$encoded = <<<'JSON'
{
    "name": "Andreas M\u00f6ller",
    "url": "https:\/\/localheinz.com"
}
JSON;

$json = Normalizer\Json::fromEncoded($encoded);

$jsonEncodeOptions = Normalizer\Format\JsonEncodeOptions::fromInt(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$normalizer = new Normalizer\JsonEncodeNormalizer($jsonEncodeOptions);

$normalized = $normalizer->normalize($json);
```

The normalized version will now be encoded with `$jsonEncodeOptions`.

:bulb: For reference, see [`json_encode()`](http://php.net/manual/en/function.json-encode.php) and the corresponding [JSON constants](http://php.net/manual/en/json.constants.php).

#### `NoFinalNewLineNormalizer`

When you want to ensure that a JSON file does not have a final new line, you can use the `NoFinalNewLineNormalizer`.

```php
<?php

use Ergebnis\Json\Normalizer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}


JSON;

$json = Normalizer\Json::fromEncoded($encoded);

$normalizer = new Normalizer\NoFinalNewLineNormalizer();

$normalized = $normalizer->normalize($json);
```

The normalized version will now not have a final new line or any whitespace at the end.

#### `SchemaNormalizer`

When you want to rebuild a JSON file according to a JSON schema, you can use the `SchemaNormalizer`.

Let's assume the following schema

```json
{
    "type": "object",
    "additionalProperties": false,
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

use Ergebnis\Json\Normalizer;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

$encoded = <<<'JSON'
{
    "url": "https://localheinz.com",
    "name": "Andreas Möller"
}
JSON;

$json = Normalizer\Json::fromEncoded($encoded);

$normalizer = new Normalizer\SchemaNormalizer(
    'file:///schema/example.json',
    new SchemaStorage(),
    new Normalizer\Validator\SchemaValidator(new Validator())
);

$normalized = $normalizer->normalize($json);
```

The normalized version will now be structured according to the JSON schema (in this simple case, properties will be reordered). Internally, the `SchemaNormalizer` uses [`justinrainbow/json-schema`](https://github.com/justinrainbow/json-schema) to resolve schemas, as well as to ensure (before and after normalization) that the JSON document is valid.

:bulb: For more information about JSON schema, visit [json-schema.org](http://json-schema.org).

### Vendor-specific normalizers

This package comes with the following vendor-specific normalizers:

* [`Ergebnis\Json\Normalizer\Vendor\Composer\JsonNormalizer`](#vendorcomposercomposerjsonnormalizer)

#### `Vendor\Composer\ComposerJsonNormalizer`

The `Vendor\Composer\ComposerJsonNormalizer` can be used to normalize a `composer.json` file according to its underlying JSON schema.

It composes the following normalizers:

* [`Ergebnis\Composer\Json\Normalizer\Vendor\Composer\BinNormalizer`](#vendorcomposerbinnormalizer)
* [`Ergebnis\Composer\Json\Normalizer\Vendor\Composer\ConfigHashNormalizer`](#vendorcomposerconfighashnormalizer)
* [`Ergebnis\Composer\Json\Normalizer\Vendor\Composer\PackageHashNormalizer`](#vendorcomposerpackagehashnormalizer)
* [`Ergebnis\Composer\Json\Normalizer\Vendor\Composer\VersionConstraintNormalizer`](#vendorcomposerversionconstraintnormalizer)

#### `Vendor\Composer\BinNormalizer`

When `composer.json` contains an array of scripts in the `bin` section, the `Vendor\Composer\BinNormalizer` will sort the elements of the `bin` section by value in ascending order.

:bulb: Find out more about the `bin` section at [Composer: The composer.json schema](https://getcomposer.org/doc/04-schema.md#bin).

#### `Vendor\Composer\ConfigHashNormalizer`

When `composer.json` contains any configuration in the

* `config`
* `extra`
* `scripts-descriptions`

sections, the `Vendor\Composer\ConfigHashNormalizer` will sort the content of these sections by key in ascending order. If a value is an object, it will continue to sort its properties by name.

:bulb: Find out more about the `config` section at [Composer: The composer.json schema](https://getcomposer.org/doc/06-config.md).

#### `Vendor\Composer\PackageHashNormalizer`

When `composer.json` contains any configuration in the

* `conflict`
* `provide`
* `replace`
* `require`
* `require-dev`
* `suggest`

sections, the `Vendor\Composer\PackageHashNormalizer` will sort the content of these sections.

:bulb: This transfers the behaviour from using the `--sort-packages` or `sort-packages` configuration flag to other sections. Find out more about the `--sort-packages` flag and configuration at [Composer: Config](https://getcomposer.org/doc/06-config.md#sort-packages) and [Composer: Command Line Interface / Commands](https://getcomposer.org/doc/03-cli.md#require).

#### `Vendor\Composer\VersionConstraintNormalizer`

When `composer.json` contains version constraints in the

* `conflict`
* `provide`
* `replace`
* `require`
* `require-dev`

sections, the `Vendor\Composer\VersionConstraintNormalizer` will ensure that

* all constraints are trimmed
* *and* constraints are separated by a single space (` `) or a comma (`,`)
* *or* constraints are separated by double-pipe with a single space before and after (` || `)
* *range* constraints are separated by a single space (` `)

:bulb: Find out more about version constraints at [Composer: Version and Constraints](https://getcomposer.org/doc/articles/versions.md).

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](https://github.com/ergebnis/.github/blob/main/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.

Please have a look at [`LICENSE.md`](LICENSE.md).

## Credits

The algorithm for sorting packages in the [`Vendor\Composer\PackageHashNormalizer`](src/Vendor/Composer/PackageHashNormalizer.php) has been adopted from [`Composer\Json\JsonManipulator::sortPackages()`](https://github.com/composer/composer/blob/1.6.2/src/Composer/Json/JsonManipulator.php#L110-L146) (originally licensed under MIT by [Nils Adermann](https://github.com/naderman) and [Jordi Boggiano](https://github.com/seldaek)), which I initially contributed to `composer/composer` with [`composer/composer#3549`](https://github.com/composer/composer/pull/3549) and [`composer/composer#3872`](https://github.com/composer/composer/pull/3872).

## Curious what I am building?

:mailbox_with_mail: [Subscribe to my list](https://localheinz.com/projects/), and I will occasionally send you an email to let you know what I am working on.
