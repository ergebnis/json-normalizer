# json-normalizer

[![Build Status](https://travis-ci.org/localheinz/json-normalizer.svg?branch=master)](https://travis-ci.org/localheinz/json-normalizer)
[![codecov](https://codecov.io/gh/localheinz/json-normalizer/branch/master/graph/badge.svg)](https://codecov.io/gh/localheinz/json-normalizer)
[![Latest Stable Version](https://poser.pugx.org/localheinz/json-normalizer/v/stable)](https://packagist.org/packages/localheinz/json-normalizer)
[![Total Downloads](https://poser.pugx.org/localheinz/json-normalizer/downloads)](https://packagist.org/packages/localheinz/json-normalizer)

Provides normalizers for normalizing JSON documents.

## Installation

Run

```
$ composer require localheinz/json-normalizer
```

## Usage

This package comes with the following normalizers:

* [`Localheinz\Json\Normalizer\AutoFormatNormalizer`](#autoformatnormalizer)
* [`Localheinz\Json\Normalizer\CallableNormalizer`](#callablenormalizer)
* [`Localheinz\Json\Normalizer\ChainNormalizer`](#chainnormalizer)
* [`Localheinz\Json\Normalizer\FinalNewLineNormalizer`](#finalnewlinenormalizer)
* [`Localheinz\Json\Normalizer\FixedFormatNormalizer`](#fixedformatnormalizer)
* [`Localheinz\Json\Normalizer\IndentNormalizer`](#indentnormalizer)
* [`Localheinz\Json\Normalizer\JsonEncodeNormalizer`](#jsonencodenormalizer)
* [`Localheinz\Json\Normalizer\NoFinalNewLineNormalizer`](#nofinalnewlinenormalizer)
* [`Localheinz\Json\Normalizer\SchemaNormalizer`](#schemanormalizer)

:bulb: All of these normalizers implement the `Localheinz\Json\Normalizer\NormalizerInterface`.

### `AutoFormatNormalizer`

If you want to normalize a JSON file with an implementation of `NormalizerInterface`, but
retain the original formatting, you can use the `AutoFormatNormalizer`.

```php
use Localheinz\Json\Normalizer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

$json = Normalizer\Json::fromEncoded($encoded);

/** @var Normalizer\NormalizerInterface $composedNormalizer*/
$normalizer = new Normalizer\AutoFormatNormalizer($composedNormalizer);

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

### `CallableNormalizer`

If you want to normalize a JSON file with a `callable`, you can use the `CallableNormalizer`.

```php
use Localheinz\Json\Normalizer;

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

### `ChainNormalizer`

If you want to apply multiple normalizers in a chain, you can use the `ChainNormalizer`.

```php
use Localheinz\Json\Normalizer;

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
    new Normalizer\IndentNormalizer($indent),
    new Normalizer\FinalNewLineNormalizer()
);

$normalized = $normalizer->normalize($json);
```

The normalized version will now contain the result of applying all normalizers in a chain, one after another.

:bulb: Be careful with the order of the normalizers, as one normalizer might override changes a previous normalizer applied.

### `FinalNewLineNormalizer`

If you want to ensure that a JSON file has a single final new line, you can use the `FinalNewLineNormalizer`.

```php
use Localheinz\Json\Normalizer;

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

### `FixedFormatNormalizer`

If you want to normalize a JSON file with an implementation of `NormalizerInterface`, but
apply a fixed formatting, you can use the `FixedFormatNormalizer`.

```php
use Localheinz\Json\Normalizer;

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
    $format
);

$normalized = $normalizer->normalize($json);
```

The normalized version will now have the composed normalizer applied,
but also apply the formatting according to `$format`.

:bulb: Alternatively, you can use the [`AutoFormatNormalizer`](#autoformatnormalizer).

### `IndentNormalizer`

If you need to adjust the indentation of a JSON file, you can use the `IndentNormalizer`.

```php
use Localheinz\Json\Normalizer;

$encoded = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

$json = Normalizer\Json::fromEncoded($encoded);

$indent = Normalizer\Format\Indent::fromString('  ');

$normalizer = new Normalizer\IndentNormalizer($indent);

$normalized = $normalizer->normalize($json);
```

The normalized version will now be indented with 2 spaces.

### `JsonEncodeNormalizer`

If you need to adjust the encoding of a JSON file, you can use the `JsonEncodeNormalizer`.

```php
use Localheinz\Json\Normalizer;

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

### `NoFinalNewLineNormalizer`

If you want to ensure that a JSON file does not have a final new line, you can use the `FinalNewLineNormalizer`.

```php
use Localheinz\Json\Normalizer;

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

### `SchemaNormalizer`

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
use Localheinz\Json\Normalizer;

$encoded = <<<'JSON'
{
    "url": "https://localheinz.com",
    "name": "Andreas Möller"
}
JSON;

$json = Normalizer\Json::fromEncoded($encoded);

$normalizer = new Normalizer\SchemaNormalizer('file:///schema/example.json');

$normalized = $normalizer->normalize($json);
```

The normalized version will now be structured according to the JSON
schema (in this simple case, properties will be reordered). Internally,
the `SchemaNormalizer` uses [`justinrainbow/json-schema`](https://github.com/justinrainbow/json-schema)
to resolve schemas, as well as to ensure (before and after normalization)
that the JSON document is valid.

:bulb: For more information about JSON schema, visit [json-schema.org](http://json-schema.org).

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](.github/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.
