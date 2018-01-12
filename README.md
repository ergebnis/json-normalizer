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

* [`Localheinz\Json\Normalizer\CallableNormalizer`](#callablenormalizer)
* [`Localheinz\Json\Normalizer\FinalNewLineNormalizer`](#finalnewlinenormalizer)
* [`Localheinz\Json\Normalizer\IndentNormalizer`](#indentnormalizer)
* [`Localheinz\Json\Normalizer\JsonEncodeNormalizer`](#jsonencodenormalizer)
* [`Localheinz\Json\Normalizer\NoFinalNewLineNormalizer`](#nofinalnewlinenormalizer)

:bulb: All of these normalizers implement the `Localheinz\Json\Normalizer\NormalizerInterface`. 

### `CallableNormalizer`

If you want to normalize a JSON file with a `callable`, you can use the `CallableNormalizer`.

```php
use Localheinz\Json\Normalizer;

$json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

$callable = function (string $json): string {
    $decoded = json_decode($json);

    foreach (get_object_vars($decoded) as $name => $value) {
        if ('https://localheinz.com' !== $value) {
            continue;
        }
        
        $decoded->{$name} .= '/open-source/';
    }

    return json_encode($decoded);
};

$normalizer = new Normalizer\CallableNormalizer($callable);

$normalized = $normalizer->normalize($json);
```

The normalized version will now have the callable applied to it.

### `FinalNewLineNormalizer`

If you want to ensure that a JSON file has a single final new line, you can use the `FinalNewLineNormalizer`.

```php
use Localheinz\Json\Normalizer;

$json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}


JSON;

$normalizer = new Normalizer\FinalNewLineNormalizer();

$normalized = $normalizer->normalize($json);
```

The normalized version will now have a single final new line.

### `IndentNormalizer`

If you need to adjust the indentation of a JSON file, you can use the `IndentNormalizer`.

```php
use Localheinz\Json\Normalizer;

$json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

$indent = '  ';

$normalizer = new Normalizer\IndentNormalizer($indent);

$normalized = $normalizer->normalize($json);
```

The normalized version will now be indented with 2 spaces.

### `JsonEncodeNormalizer`

If you need to adjust the encoding of a JSON file, you can use the `JsonEncodeNormalizer`.

```php
use Localheinz\Json\Normalizer;

$json = <<<'JSON'
{
    "name": "Andreas M\u00f6ller",
    "url": "https:\/\/localheinz.com"
}
JSON;

$jsonEncodeOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

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

$json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}


JSON;

$normalizer = new Normalizer\NoFinalNewLineNormalizer();

$normalized = $normalizer->normalize($json);
```

The normalized version will now not have a final new line or any whitespace at the end.

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](.github/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.
