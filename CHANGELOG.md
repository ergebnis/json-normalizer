# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`1.0.3...main`][1.0.3...main].

## [`1.0.3`][1.0.3]

For a full diff see [`1.0.2...1.0.3`][1.0.2...1.0.3].

### Fixed

* Adjusted `Vendor\Composer\PackageHashNormalizer` to take into account the newly addded `composer-plugin-api` as platform requirement ([#463]), by [@localheinz]

## [`1.0.2`][1.0.2]

For a full diff see [`1.0.1...1.0.2`][1.0.1...1.0.2].

### Fixed

* Adjusted `Vendor\Composer\ConfigHashNormalizer` to take into account the full property path, not only the property name ([#429]), by [@localheinz]

## [`1.0.1`][1.0.1]

For a full diff see [`1.0.0...1.0.1`][1.0.0...1.0.1].

### Fixed

* Adjusted `Vendor\Composer\ConfigHashNormalizer` to ignore the `preferred-install` hash ([#425]), by [@localheinz]

## [`1.0.0`][1.0.0]

For a full diff see [`0.14.1...1.0.0`][0.14.1...1.0.0].

### Changed

* Adjusted `Vendor\Composer\ConfigHashNormalizer` to recursively sort hashes by key ([#424]), by [@localheinz]

## [`0.14.1`][0.14.1]

For a full diff see [`0.14.0...0.14.1`][0.14.0...0.14.1].

### Fixed

* Adjusted `Vendor\Composer\ConfigHashNormalizer` to continue normalizing properties when a property has an empty value ([#423]), by [@localheinz]

## [`0.14.0`][0.14.0]

For a full diff see [`0.13.1...0.14.0`][0.13.1...0.14.0].

### Added

* Extracted an `Indent::CHARACTERS` constant that exposes a map of indent styles to indent characters ([#384]), by [@localheinz]

## [`0.13.1`][0.13.1]

For a full diff see [`0.13.0...0.13.1`][0.13.0...0.13.1].

### Changed

* Dropped support for PHP 7.1 ([#335]), by [@localheinz]

## [`0.13.0`][0.13.0]

For a full diff see [`0.12.0...0.13.0`][0.12.0...0.13.0].

### Added

* Added support for PHP 8.0 ([#308]), by [@localheinz]

## [`0.12.0`][0.12.0]

For a full diff see [`0.11.0...0.12.0`][0.11.0...0.12.0].

### Added

* Added `SchemaValidator::validate()`, which returns a `Result` composing validation error messages ([#268]), by [@localheinz]

### Deprecated

* Deprecated `SchemaValidator::isValid()` ([#269]), by [@localheinz]

## [`0.11.0`][0.11.0]

For a full diff see [`0.10.1...0.11.0`][0.10.1...0.11.0].

### Added

* Merged in normalizers from [`ergebnis/composer-json-normalizer`](https://github.com/ergebnis/composer-json-normalizer) ([#203]), by [@localheinz]

### Removed

* Removed the `ChainUriRetriever` ([#202]), by [@localheinz]

## [`0.10.1`][0.10.1]

For a full diff see [`0.10.0...0.10.1`][0.10.0...0.10.1].

### Fixed

* Brought back support for PHP 7.1 ([#191]), by [@localheinz]

## [`0.10.0`][0.10.0]

For a full diff see [`0.9.0...0.10.0`][0.9.0...0.10.0].

### Added

* Added a `ChainUriRetriever` which allows specifying multiple URI retrievers ([#102]), by [@localheinz]
* Added this changelog ([#103]), by [@localheinz]

### Changed

* Allowing injection of a `UriRetriever` into the `SchemaNormalizer`, and defaulting to a `ChainUriRetriever` which composes `FileGetContents` and `Curl` URI retrievers ([#104]), by [@localheinz]
* Dropped `null` default values of constructor arguments of `AutoFormatNormalizer`, `FixedFormatNormalizer`, `Formatter`, `IndentNormalizer` to expose hard dependencies ([#109]), by [@localheinz]
* Dropped nullable return type declaration from `ChainUriRetriever::getContentType()`, defaulting to an empty `string` when `ChainUriRetriever::retrieve()` wasn't invoked yet ([#132]), by [@localheinz]
* Started using `ergebnis/json-printer` instead of `localheinz/json-printer` ([#176]), by [@localheinz]
* Renamed vendor namespace `Localheinz` to `Ergebnis` after move to [@ergebnis] ([#181]), by [@localheinz]

  Run

  ```
  $ composer remove localheinz/json-normalizer
  ```

  and

  ```
  $ composer require ergebnis/json-normalizer
  ```

  to update.

  Run

  ```
  $ find . -type f -exec sed -i '.bak' 's/Localheinz\\Json\\Normalizer/Ergebnis\\Json\\Normalizer/g' {} \;
  ```

  to replace occurrences of `Localheinz\Json\Normalizer` with `Ergebnis\Json\Normalizer`.

  Run

  ```
  $ find -type f -name '*.bak' -delete
  ```

  to delete backup files created in the previous step.

### Fixed

* Dropped support for PHP 7.1 ([#163]), by [@localheinz]

## [`0.9.0`][0.9.0]

For a full diff see [`0.8.0...0.9.0`][0.8.0...0.9.0].

### Added

* Added `JsonEncodeOptions` value object ([#93]), by [@localheinz]

### Changed

* Turned method on `Format` into named constructor on `Indent` value object ([#94]), by [@localheinz]
* Turned method on `Format` into named constructor on `JsonEncodeOptions` value object([#95]), by [@localheinz]
* Turned method on `Format` into named constructor on `NewLine` value object ([#96]), by [@localheinz]

### Removed

* Removed capability to create `Json` value object from decoded data ([#88]), by [@localheinz]
* Removed `IndentInterface` ([#89]), by [@localheinz]
* Removed `NewLineInterface` ([#90]), by [@localheinz]
* Removed `FormatInterface` ([#91]), by [@localheinz]
* Removed `JsonInterface` ([#92]), by [@localheinz]

## [`0.8.0`][0.8.0]

For a full diff see [`0.7.0...0.8.0`][0.7.0...0.8.0].

### Added

* Added named constructor to `Json` value object to allow creation from data ([#86]), by [@localheinz]

### Changed

* Renamed `InvalidJsonException` to `InvalidJsonEncodedException` ([#85]), by [@localheinz]

### Fixed

* `ExceptionInterface` now extends `Throwable` ([#82]), by [@BackEndTea]
* Extension `ext/json` is now explicitly required  ([#84]), by [@localheinz]

## [`0.7.0`][0.7.0]

For a full diff see [`0.6.0...0.7.0`][0.6.0...0.7.0].

### Added

* Added `Indent` value object ([#73]), by [@localheinz]
* Added `NewLine` value object ([#76]), by [@localheinz]
* Added `Json` value object ([#64]), by [@localheinz]
* Added exceptions ([#79]), by [@localheinz]

### Changed

* Removed the `Sniffer` in favour of a named constructor on `Format` value object ([#77]), by [@localheinz]

### Fixed

* Added more test cases for sniffing JSON without whitespace ([#67]), by [@localheinz]
* Added missing types in a Docblock ([#68]), by [@localheinz]
* The `Format` value object now rejects mixed tabs and spaces as indent ([#69]), by [@localheinz]
* Added more test cases for JSON without indent ([#72]), by [@localheinz]
* Sniff only pure indents, no mixed spaces and tabs ([#71]), by [@localheinz]

## [`0.6.0`][0.6.0]

For a full diff see [`0.5.2...0.6.0`][0.5.2...0.6.0].

### Added

* Added sniffing of new-line character sequence ([#55]), by [@localheinz]

## [`0.5.2`][0.5.2]

For a full diff see [`0.5.1...0.5.2`][0.5.1...0.5.2].

### Fixed

* Keep resolving references until there are none left ([#49]), by [@localheinz]

## [`0.5.1`][0.5.1]

For a full diff see [`0.5.0...0.5.1`][0.5.0...0.5.1].

### Fixed

* Resolve referenced schema in `oneOf` combination ([#47]), by [@localheinz]

## [`0.5.0`][0.5.0]

For a full diff see [`0.4.0...0.5.0`][0.4.0...0.5.0].

### Added

* Added handling of arrays where schema describes tuple ([#37]), by [@localheinz]
* Added handling of arrays were schema has reference definition ([#40]), by [@localheinz]
* Added handling of `oneOf` ([#45]), by [@localheinz]

## [`0.4.0`][0.4.0]

For a full diff see [`0.3.0...0.4.0`][0.3.0...0.4.0].

### Added

* Extracted `Formatter` ([#31]), by [@localheinz]

### Changed

* Renamed `FormatSniffer` to `Sniffer` ([#30]), by [@localheinz]

## [`0.3.0`][0.3.0]

For a full diff see [`0.2.0...0.3.0`][0.2.0...0.3.0].

### Changed

* Require PHP 7.1 ([#27]), by [@localheinz]
* Allow to mutate `Format` value object ([#29]), by [@localheinz]

## [`0.2.0`][0.2.0]

For a full diff see [`0.1.0...0.2.0`][0.1.0...0.2.0].

### Added

* Added `FixedFormatNormalizer` ([#17]), by [@localheinz]

## [`0.1.0`][0.1.0]

For a full diff see [`5d8b3e2...0.1.0`][5d8b3e2...0.1.0].

### Added

* Added `IndentNormalizer` ([#1]), by [@localheinz]
* Added `FinalNewLineNormalizer` ([#2]), by [@localheinz]
* Added `NoFinalNewLineNormalizer` ([#3]), by [@localheinz]
* Added `JsonEncodeNormalizer` ([#7]), by [@localheinz]
* Added `CallableNormalizer` ([#8]), by [@localheinz]
* Added `ChainNormalizer` ([#9]), by [@localheinz]
* Added `Format` value object ([#10]), by [@localheinz]
* Added `FormatSniffer` ([#12]), by [@localheinz]
* Added `AutoFormatNormalizer` ([#13]), by [@localheinz]
* Added `SchemaNormalizer` ([#15]), by [@localheinz]

[0.1.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.1.0
[0.2.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.2.0
[0.3.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.3.0
[0.4.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.4.0
[0.5.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.5.0
[0.5.1]: https://github.com/ergebnis/json-normalizer/releases/tag/0.5.1
[0.5.2]: https://github.com/ergebnis/json-normalizer/releases/tag/0.5.2
[0.6.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.6.0
[0.7.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.7.0
[0.8.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.8.0
[0.9.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.9.0
[0.10.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.10.0
[0.10.1]: https://github.com/ergebnis/json-normalizer/releases/tag/0.10.0
[0.11.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.11.0
[0.12.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.12.0
[0.13.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.13.0
[0.13.1]: https://github.com/ergebnis/json-normalizer/releases/tag/0.13.1
[0.14.0]: https://github.com/ergebnis/json-normalizer/releases/tag/0.14.0
[0.14.1]: https://github.com/ergebnis/json-normalizer/releases/tag/0.14.1
[1.0.0]: https://github.com/ergebnis/json-normalizer/releases/tag/1.0.0
[1.0.1]: https://github.com/ergebnis/json-normalizer/releases/tag/1.0.1
[1.0.2]: https://github.com/ergebnis/json-normalizer/releases/tag/1.0.2

[5d8b3e2...0.1.0]: https://github.com/ergebnis/json-normalizer/compare/5d8b3e2...0.1.0
[0.1.0...0.2.0]: https://github.com/ergebnis/json-normalizer/compare/0.1.0...0.2.0
[0.2.0...0.3.0]: https://github.com/ergebnis/json-normalizer/compare/0.2.0...0.3.0
[0.3.0...0.4.0]: https://github.com/ergebnis/json-normalizer/compare/0.3.0...0.4.0
[0.4.0...0.5.0]: https://github.com/ergebnis/json-normalizer/compare/0.4.0...0.5.0
[0.5.0...0.5.1]: https://github.com/ergebnis/json-normalizer/compare/0.5.0...0.5.1
[0.5.1...0.5.2]: https://github.com/ergebnis/json-normalizer/compare/0.5.1...0.5.2
[0.5.2...0.6.0]: https://github.com/ergebnis/json-normalizer/compare/0.5.2...0.6.0
[0.6.0...0.7.0]: https://github.com/ergebnis/json-normalizer/compare/0.6.0...0.7.0
[0.7.0...0.8.0]: https://github.com/ergebnis/json-normalizer/compare/0.7.0...0.8.0
[0.8.0...0.9.0]: https://github.com/ergebnis/json-normalizer/compare/0.8.0...0.9.0
[0.9.0...0.10.0]: https://github.com/ergebnis/json-normalizer/compare/0.9.0...0.10.0
[0.10.0...0.10.1]: https://github.com/ergebnis/json-normalizer/compare/0.10.0...0.10.1
[0.10.1...0.11.0]: https://github.com/ergebnis/json-normalizer/compare/0.10.1...0.11.0
[0.11.0...0.12.0]: https://github.com/ergebnis/json-normalizer/compare/0.11.0...0.12.0
[0.12.0...0.13.0]: https://github.com/ergebnis/json-normalizer/compare/0.12.0...0.13.0
[0.13.0...0.13.1]: https://github.com/ergebnis/json-normalizer/compare/0.13.0...0.13.1
[0.13.1...0.14.0]: https://github.com/ergebnis/json-normalizer/compare/0.13.1...0.14.0
[0.14.0...0.14.1]: https://github.com/ergebnis/json-normalizer/compare/0.14.0...0.14.1
[0.14.1...1.0.0]: https://github.com/ergebnis/json-normalizer/compare/0.14.1...1.0.0
[1.0.0...1.0.1]: https://github.com/ergebnis/json-normalizer/compare/1.0.0...1.0.0
[1.0.1...1.0.2]: https://github.com/ergebnis/json-normalizer/compare/1.0.1...1.0.2
[1.0.2...1.0.3]: https://github.com/ergebnis/json-normalizer/compare/1.0.2...1.0.3
[1.0.3...main]: https://github.com/ergebnis/json-normalizer/compare/1.0.3...main

[#1]: https://github.com/ergebnis/json-normalizer/pull/1
[#2]: https://github.com/ergebnis/json-normalizer/pull/2
[#3]: https://github.com/ergebnis/json-normalizer/pull/3
[#7]: https://github.com/ergebnis/json-normalizer/pull/7
[#8]: https://github.com/ergebnis/json-normalizer/pull/8
[#9]: https://github.com/ergebnis/json-normalizer/pull/9
[#10]: https://github.com/ergebnis/json-normalizer/pull/10
[#12]: https://github.com/ergebnis/json-normalizer/pull/12
[#13]: https://github.com/ergebnis/json-normalizer/pull/13
[#15]: https://github.com/ergebnis/json-normalizer/pull/15
[#17]: https://github.com/ergebnis/json-normalizer/pull/17
[#27]: https://github.com/ergebnis/json-normalizer/pull/27
[#29]: https://github.com/ergebnis/json-normalizer/pull/29
[#30]: https://github.com/ergebnis/json-normalizer/pull/30
[#31]: https://github.com/ergebnis/json-normalizer/pull/31
[#37]: https://github.com/ergebnis/json-normalizer/pull/37
[#40]: https://github.com/ergebnis/json-normalizer/pull/40
[#45]: https://github.com/ergebnis/json-normalizer/pull/45
[#47]: https://github.com/ergebnis/json-normalizer/pull/47
[#49]: https://github.com/ergebnis/json-normalizer/pull/49
[#55]: https://github.com/ergebnis/json-normalizer/pull/55
[#64]: https://github.com/ergebnis/json-normalizer/pull/64
[#67]: https://github.com/ergebnis/json-normalizer/pull/67
[#68]: https://github.com/ergebnis/json-normalizer/pull/68
[#69]: https://github.com/ergebnis/json-normalizer/pull/69
[#71]: https://github.com/ergebnis/json-normalizer/pull/71
[#72]: https://github.com/ergebnis/json-normalizer/pull/72
[#73]: https://github.com/ergebnis/json-normalizer/pull/73
[#76]: https://github.com/ergebnis/json-normalizer/pull/76
[#77]: https://github.com/ergebnis/json-normalizer/pull/77
[#79]: https://github.com/ergebnis/json-normalizer/pull/79
[#82]: https://github.com/ergebnis/json-normalizer/pull/82
[#84]: https://github.com/ergebnis/json-normalizer/pull/84
[#85]: https://github.com/ergebnis/json-normalizer/pull/85
[#86]: https://github.com/ergebnis/json-normalizer/pull/86
[#88]: https://github.com/ergebnis/json-normalizer/pull/88
[#89]: https://github.com/ergebnis/json-normalizer/pull/89
[#90]: https://github.com/ergebnis/json-normalizer/pull/90
[#91]: https://github.com/ergebnis/json-normalizer/pull/91
[#92]: https://github.com/ergebnis/json-normalizer/pull/92
[#93]: https://github.com/ergebnis/json-normalizer/pull/93
[#94]: https://github.com/ergebnis/json-normalizer/pull/94
[#95]: https://github.com/ergebnis/json-normalizer/pull/95
[#96]: https://github.com/ergebnis/json-normalizer/pull/96
[#102]: https://github.com/ergebnis/json-normalizer/pull/102
[#103]: https://github.com/ergebnis/json-normalizer/pull/103
[#104]: https://github.com/ergebnis/json-normalizer/pull/104
[#109]: https://github.com/ergebnis/json-normalizer/pull/109
[#132]: https://github.com/ergebnis/json-normalizer/pull/132
[#163]: https://github.com/ergebnis/json-normalizer/pull/163
[#176]: https://github.com/ergebnis/json-normalizer/pull/176
[#181]: https://github.com/ergebnis/json-normalizer/pull/181
[#191]: https://github.com/ergebnis/json-normalizer/pull/191
[#202]: https://github.com/ergebnis/json-normalizer/pull/202
[#203]: https://github.com/ergebnis/json-normalizer/pull/203
[#268]: https://github.com/ergebnis/json-normalizer/pull/268
[#269]: https://github.com/ergebnis/json-normalizer/pull/269
[#308]: https://github.com/ergebnis/json-normalizer/pull/308
[#335]: https://github.com/ergebnis/json-normalizer/pull/335
[#384]: https://github.com/ergebnis/json-normalizer/pull/384
[#423]: https://github.com/ergebnis/json-normalizer/pull/423
[#424]: https://github.com/ergebnis/json-normalizer/pull/424
[#425]: https://github.com/ergebnis/json-normalizer/pull/425
[#429]: https://github.com/ergebnis/json-normalizer/pull/429
[#463]: https://github.com/ergebnis/json-normalizer/pull/463

[@BackEndTea]: https://github.com/BackEndTea
[@ergebnis]: https://github.com/ergebnis
[@localheinz]: https://github.com/localheinz
