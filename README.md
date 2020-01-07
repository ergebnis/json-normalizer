# json-normalizer

[![Continuous Deployment](https://github.com/ergebnis/json-normalizer/workflows/Continuous%20Deployment/badge.svg)](https://github.com/ergebnis/json-normalizer/actions)
[![Continuous Integration](https://github.com/ergebnis/json-normalizer/workflows/Continuous%20Integration/badge.svg)](https://github.com/ergebnis/json-normalizer/actions)
[![Code Coverage](https://codecov.io/gh/ergebnis/json-normalizer/branch/master/graph/badge.svg)](https://codecov.io/gh/ergebnis/json-normalizer)
[![Type Coverage](https://shepherd.dev/github/ergebnis/json-normalizer/coverage.svg)](https://shepherd.dev/github/ergebnis/json-normalizer)
[![Latest Stable Version](https://poser.pugx.org/ergebnis/json-normalizer/v/stable)](https://packagist.org/packages/ergebnis/json-normalizer)
[![Total Downloads](https://poser.pugx.org/ergebnis/json-normalizer/downloads)](https://packagist.org/packages/ergebnis/json-normalizer)

Provides normalizers for normalizing JSON documents.

## Installation

Run

```
$ composer require ergebnis/json-normalizer
```

## Usage

### Generic

This package comes with the following generic normalizers:

* [`Ergebnis\Json\Normalizer\AutoFormatNormalizer`](#auto-format-normalizer)
* [`Ergebnis\Json\Normalizer\CallableNormalizer`](#callable-normalizer)
* [`Ergebnis\Json\Normalizer\ChainNormalizer`](#chain-normalizer)
* [`Ergebnis\Json\Normalizer\FinalNewLineNormalizer`](#final-new-line-normalizer)
* [`Ergebnis\Json\Normalizer\FixedFormatNormalizer`](#fixed-format-normalizer)
* [`Ergebnis\Json\Normalizer\IndentNormalizer`](#indent-normalizer)
* [`Ergebnis\Json\Normalizer\JsonEncodeNormalizer`](#json-encode-normalizer)
* [`Ergebnis\Json\Normalizer\NoFinalNewLineNormalizer`](#no-final-new-line-normalizer)
* [`Ergebnis\Json\Normalizer\SchemaNormalizer`](#schema-normalizer)

:bulb: All of these normalizers implement the `Ergebnis\Json\Normalizer\NormalizerInterface`.

#### <a name="#auto-format-normalizer"></a> `AutoFormatNormalizer`

If you want to normalize a JSON file with an implementation of `NormalizerInterface`, but
retain the original formatting, you can use the `AutoFormatNormalizer`.

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

The normalized version will now have the composed normalizer applied,
but also retained the original formatting (within certain limits). Before
applying the composer normalizer, the `AutoFormatNormalizer` will attempt
to detect the following:

* `json_encode()` options
* indent
* whether a final new line exists or not

After applying the composed normalizer, the `AutoFormatNormalizer` will

* decode with `json_decode()` and encode again with `json_encode()`, passing in the previously detected options
* indent with the detected indent
* add a final new line of detected

:bulb: Alternatively, you can use the [`FixedFormatNormalizer`](#fixedformatnormalizer).

#### <a name="#callable-normalizer"></a> `CallableNormalizer`

If you want to normalize a JSON file with a `callable`, you can use the `CallableNormalizer`.

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

#### <a name="#chain-normalizer"></a> `ChainNormalizer`

If you want to apply multiple normalizers in a chain, you can use the `ChainNormalizer`.

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

#### <a name="#final-new-line-normalizer"></a> `FinalNewLineNormalizer`

If you want to ensure that a JSON file has a single final new line, you can use the `FinalNewLineNormalizer`.

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

#### <a name="#fixed-format-normalizer"></a> `FixedFormatNormalizer`

If you want to normalize a JSON file with an implementation of `NormalizerInterface`, but
apply a fixed formatting, you can use the `FixedFormatNormalizer`.

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

The normalized version will now have the composed normalizer applied,
but also apply the formatting according to `$format`.

:bulb: Alternatively, you can use the [`AutoFormatNormalizer`](#autoformatnormalizer).

#### <a name="#indent-normalizer"></a> `IndentNormalizer`

If you need to adjust the indentation of a JSON file, you can use the `IndentNormalizer`.

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

#### <a name="#json-encode-normalizer"></a> `JsonEncodeNormalizer`

If you need to adjust the encoding of a JSON file, you can use the `JsonEncodeNormalizer`.

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

:bulb: For reference, see [`json_encode()`](http://php.net/manual/en/function.json-encode.php)
and the corresponding [JSON constants](http://php.net/manual/en/json.constants.php).

#### <a name="#no-final-new-line-normalizer"></a> `NoFinalNewLineNormalizer`

If you want to ensure that a JSON file does not have a final new line, you can use the `FinalNewLineNormalizer`.

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

#### <a name="#schema-normalizer"></a> `SchemaNormalizer`

If you want to rebuild a JSON file according to a JSON schema, you can use the `SchemaNormalizer`.

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

The normalized version will now be structured according to the JSON
schema (in this simple case, properties will be reordered). Internally,
the `SchemaNormalizer` uses [`justinrainbow/json-schema`](https://github.com/justinrainbow/json-schema)
to resolve schemas, as well as to ensure (before and after normalization)
that the JSON document is valid.

:bulb: For more information about JSON schema, visit [json-schema.org](http://json-schema.org).

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](https://github.com/ergebnis/.github/blob/master/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.
