# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/localheinz/json-normalizer/compare/0.9.0...HEAD)

For a full diff see [`0.9.0...master`](https://github.com/localheinz/json-normalizer/compare/0.9.0...master).

### Added

* Added a `ChainUriRetriever` which allows specifying multiple URI retrievers ([#102](https://github.com/localheinz/json-normalizer/pull/102)), by [@localheinz](https://github.com/localheinz)
* Added this changelog ([#103](https://github.com/localheinz/json-normalizer/pull/103)), by [@localheinz](https://github.com/localheinz)

### Changed

* Allowing injection of a `UriRetriever` into the `SchemaNormalizer`, and defaulting to a `ChainUriRetriever` which composes `FileGetContents` and `Curl` URI retrievers ([#104](https://github.com/localheinz/json-normalizer/pull/104)), by [@localheinz](https://github.com/localheinz)
* Dropped `null` default values of constructor arguments of `AutoFormatNormalizer`, `FixedFormatNormalizer`, `Formatter`, `IndentNormalizer` to expose hard dependencies ([#109](https://github.com/localheinz/json-normalizer/pull/109)), by [@localheinz](https://github.com/localheinz)
* Dropped nullable return type declaration from `ChainUriRetriever::getContentType()`, defaulting to an empty `string` when `ChainUriRetriever::retrieve()` wasn't invoked yet ([#132](https://github.com/localheinz/json-normalizer/pull/132)), by [@localheinz](https://github.com/localheinz)

### Fixed

* Dropped support for PHP 7.1 ([#163](https://github.com/localheinz/json-normalizer/pull/163)), by [@localheinz](https://github.com/localheinz)

## [`0.9.0`](https://github.com/localheinz/json-normalizer/releases/tag/0.9.0)

For a full diff see [`0.8.0...0.9.0`](https://github.com/localheinz/json-normalizer/compare/0.8.0...0.9.0).

### Added

* Added `JsonEncodeOptions` value object ([#93](https://github.com/localheinz/json-normalizer/pull/93), by [@localheinz](https://github.com/localheinz)

### Changed

* Turned method on `Format` into named constructor on `Indent` value object ([#94](https://github.com/localheinz/json-normalizer/pull/94), by [@localheinz](https://github.com/localheinz)
* Turned method on `Format` into named constructor on `JsonEncodeOptions` value object([#95](https://github.com/localheinz/json-normalizer/pull/95), by [@localheinz](https://github.com/localheinz)
* Turned method on `Format` into named constructor on `NewLine` value object ([#96](https://github.com/localheinz/json-normalizer/pull/96), by [@localheinz](https://github.com/localheinz)

### Removed

* Removed capability to create `Json` value object from decoded data ([#88](https://github.com/localheinz/json-normalizer/pull/88), by [@localheinz](https://github.com/localheinz)
* Removed `IndentInterface` ([#89](https://github.com/localheinz/json-normalizer/pull/89), by [@localheinz](https://github.com/localheinz)
* Removed `NewLineInterface` ([#90](https://github.com/localheinz/json-normalizer/pull/90), by [@localheinz](https://github.com/localheinz)
* Removed `FormatInterface` ([#91](https://github.com/localheinz/json-normalizer/pull/91), by [@localheinz](https://github.com/localheinz)
* Removed `JsonInterface` ([#92](https://github.com/localheinz/json-normalizer/pull/92), by [@localheinz](https://github.com/localheinz)

## [`0.8.0`](https://github.com/localheinz/json-normalizer/releases/tag/0.8.0)

For a full diff see [`0.7.0...0.8.0`](https://github.com/localheinz/json-normalizer/compare/0.7.0...0.8.0).

### Added

* Added named constructor to `Json` value object to allow creation from data ([#86](https://github.com/localheinz/json-normalizer/pull/86), by [@localheinz](https://github.com/localheinz)

### Changed

* Renamed `InvalidJsonException` to `InvalidJsonEncodedException` ([#85](https://github.com/localheinz/json-normalizer/pull/85), by [@localheinz](https://github.com/localheinz)

### Fixed

* `ExceptionInterface` now extends `Throwable` ([#82](https://github.com/localheinz/json-normalizer/pull/82), by [@BackEndTea](https://github.com/BackEndTea)
* Extension `ext/json` is now explicitly required  ([#84](https://github.com/localheinz/json-normalizer/pull/84), by [@localheinz](https://github.com/localheinz)

## [`0.7.0`](https://github.com/localheinz/json-normalizer/releases/tag/0.7.0)

For a full diff see [`0.6.0...0.7.0`](https://github.com/localheinz/json-normalizer/compare/0.6.0...0.7.0).

### Added

* Added `Indent` value object ([#73](https://github.com/localheinz/json-normalizer/pull/73), by [@localheinz](https://github.com/localheinz)
* Added `NewLine` value object ([#76](https://github.com/localheinz/json-normalizer/pull/76), by [@localheinz](https://github.com/localheinz)
* Added `Json` value object ([#64](https://github.com/localheinz/json-normalizer/pull/64), by [@localheinz](https://github.com/localheinz)
* Added exceptions ([#79](https://github.com/localheinz/json-normalizer/pull/79), by [@localheinz](https://github.com/localheinz)

### Changed

* Removed the `Sniffer` in favour of a named constructor on `Format` value object ([#77](https://github.com/localheinz/json-normalizer/pull/77), by [@localheinz](https://github.com/localheinz)

### Fixed

* Added more test cases for sniffing JSON without whitespace ([#67](https://github.com/localheinz/json-normalizer/pull/67), by [@localheinz](https://github.com/localheinz)
* Added missing types in a Docblock ([#68](https://github.com/localheinz/json-normalizer/pull/68), by [@localheinz](https://github.com/localheinz)
* The `Format` value object now rejects mixed tabs and spaces as indent ([#69](https://github.com/localheinz/json-normalizer/pull/69), by [@localheinz](https://github.com/localheinz)
* Added more test cases for JSON without indent ([#72](https://github.com/localheinz/json-normalizer/pull/72), by [@localheinz](https://github.com/localheinz)
* Sniff only pure indents, no mixed spaces and tabs ([#71](https://github.com/localheinz/json-normalizer/pull/71), by [@localheinz](https://github.com/localheinz)

## [`0.6.0`](https://github.com/localheinz/json-normalizer/releases/tag/0.6.0)

For a full diff see [`0.5.2...0.6.0`](https://github.com/localheinz/json-normalizer/compare/0.5.2...0.6.0).

### Added

* Added sniffing of new-line character sequence ([#55](https://github.com/localheinz/json-normalizer/pull/55), by [@localheinz](https://github.com/localheinz)

## [`0.5.2`](https://github.com/localheinz/json-normalizer/releases/tag/0.5.2)

For a full diff see [`0.5.1...0.5.2`](https://github.com/localheinz/json-normalizer/compare/0.5.1...0.5.2).

### Fixed

* Keep resolving references until there are none left ([#49](https://github.com/localheinz/json-normalizer/pull/49), by [@localheinz](https://github.com/localheinz)

## [`0.5.1`](https://github.com/localheinz/json-normalizer/releases/tag/0.5.1)

For a full diff see [`0.5.0...0.5.1`](https://github.com/localheinz/json-normalizer/compare/0.5.0...0.5.1).

### Fixed

* Resolve referenced schema in `oneOf` combination ([#47](https://github.com/localheinz/json-normalizer/pull/47), by [@localheinz](https://github.com/localheinz)

## [`0.5.0`](https://github.com/localheinz/json-normalizer/releases/tag/0.5.0)

For a full diff see [`0.4.0...0.5.0`](https://github.com/localheinz/json-normalizer/compare/0.4.0...0.5.0).

### Added

* Added handling of arrays where schema describes tuple ([#37](https://github.com/localheinz/json-normalizer/pull/37), by [@localheinz](https://github.com/localheinz)
* Added handling of arrays were schema has reference definition ([#40](https://github.com/localheinz/json-normalizer/pull/40), by [@localheinz](https://github.com/localheinz)
* Added handling of `oneOf` ([#45](https://github.com/localheinz/json-normalizer/pull/45), by [@localheinz](https://github.com/localheinz)

## [`0.4.0`](https://github.com/localheinz/json-normalizer/releases/tag/0.4.0)

For a full diff see [`0.3.0...0.4.0`](https://github.com/localheinz/json-normalizer/compare/0.3.0...0.4.0).

### Added

* Extracted `Formatter` ([#31](https://github.com/localheinz/json-normalizer/pull/31), by [@localheinz](https://github.com/localheinz)

### Changed

* Renamed `FormatSniffer` to `Sniffer` ([#30](https://github.com/localheinz/json-normalizer/pull/30), by [@localheinz](https://github.com/localheinz)

## [`0.3.0`](https://github.com/localheinz/json-normalizer/releases/tag/0.3.0)

For a full diff see [`0.2.0...0.3.0`](https://github.com/localheinz/json-normalizer/compare/0.2.0...0.3.0).

### Changed

* Require PHP 7.1 ([#27](https://github.com/localheinz/json-normalizer/pull/27), by [@localheinz](https://github.com/localheinz)
* Allow to mutate `Format` value object ([#29](https://github.com/localheinz/json-normalizer/pull/29), by [@localheinz](https://github.com/localheinz)

## [`0.2.0`](https://github.com/localheinz/json-normalizer/releases/tag/0.2.0)

For a full diff see [`0.1.0...0.2.0`](https://github.com/localheinz/json-normalizer/compare/0.1.0...0.2.0).

### Added

* Added `FixedFormatNormalizer` ([#17](https://github.com/localheinz/json-normalizer/pull/17), by [@localheinz](https://github.com/localheinz)

## [`0.1.0`](https://github.com/localheinz/json-normalizer/releases/tag/0.1.0)

For a full diff see [`5d8b3e2...0.1.0`](https://github.com/localheinz/json-normalizer/compare/5d8b3e2...0.1.0).

### Added

* Added `IndentNormalizer` ([#1](https://github.com/localheinz/json-normalizer/pull/1), by [@localheinz](https://github.com/localheinz)
* Added `FinalNewLineNormalizer` ([#2](https://github.com/localheinz/json-normalizer/pull/2), by [@localheinz](https://github.com/localheinz)
* Added `NoFinalNewLineNormalizer` ([#3](https://github.com/localheinz/json-normalizer/pull/3), by [@localheinz](https://github.com/localheinz)
* Added `JsonEncodeNormalizer` ([#7](https://github.com/localheinz/json-normalizer/pull/7), by [@localheinz](https://github.com/localheinz)
* Added `CallableNormalizer` ([#8](https://github.com/localheinz/json-normalizer/pull/8), by [@localheinz](https://github.com/localheinz)
* Added `ChainNormalizer` ([#9](https://github.com/localheinz/json-normalizer/pull/9), by [@localheinz](https://github.com/localheinz)
* Added `Format` value object ([#10](https://github.com/localheinz/json-normalizer/pull/10), by [@localheinz](https://github.com/localheinz)
* Added `FormatSniffer` ([#12](https://github.com/localheinz/json-normalizer/pull/12), by [@localheinz](https://github.com/localheinz)
* Added `AutoFormatNormalizer` ([#13](https://github.com/localheinz/json-normalizer/pull/13), by [@localheinz](https://github.com/localheinz)
* Added `SchemaNormalizer` ([#15](https://github.com/localheinz/json-normalizer/pull/15), by [@localheinz](https://github.com/localheinz)
