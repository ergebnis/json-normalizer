# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`4.5.0...main`][4.5.0...main].

## [`4.5.0`][4.5.0]

For a full diff see [`4.4.1...4.5.0`][4.4.1...4.5.0].

### Changed

- Required `ergebnis/json:^1.2.0` ([#1073]), by [@localheinz]
- Required `ergebnis/json-printer:^3.5.0` ([#1074]), by [@localheinz]
- Required `ergebnis/json-pointer:^3.4.0` ([#1075]), by [@localheinz]
- Required `ergebnis/json-schema-validator:^4.2.0` ([#1076]), by [@localheinz]
- Added support for PHP 8.3 ([#1077]), by [@localheinz]
- Added support for PHP 7.4 ([#1079]), by [@localheinz]

## [`4.4.1`][4.4.1]

For a full diff see [`4.4.0...4.4.1`][4.4.0...4.4.1].

### Fixed

- Stopped sorting an item in `repositories` when it disables packagist ([#1039]), by [@localheinz]

## [`4.4.0`][4.4.0]

For a full diff see [`4.3.0...4.4.0`][4.3.0...4.4.0].

### Changed

- Started removing `v` prefixes from version constraints ([#1027]), by [@fredden]
- Started sorting items in the `exclude` and `only` properties of items listed in the `repositories` section ([#1001]), by [@fredden]

## [`4.3.0`][4.3.0]

For a full diff see [`4.2.0...4.3.0`][4.2.0...4.3.0].

### Changed

- Sort `allow-plugins` and `preferred-install` as sensibly as is feasible ([#980]), by [@fredden]
- Adjusted `Vendor\Composer\VersionConstraintNormalizer` to remove overlapping individual versions too ([#982]), by [@fredden]
- Added support for PHP 8.3 ([#988]), by [@localheinz]
- Required `ergebnis/json-printer:^3.4.0` ([#989]), by [@dependabot]
- Required `ergebnis/json:^1.1.0` ([#991]), by [@dependabot]
- Required `ergebnis/json-schema-validator:^4.1.0` ([#992]), by [@dependabot]

## [`4.2.0`][4.2.0]

For a full diff see [`4.1.0...4.2.0`][4.1.0...4.2.0].

### Changed

- Dropped support for PHP 8.0 ([#917]), by [@localheinz]
- Adjusted `Vendor\Composer\VersionConstraintNormalizer` to prefer tilde operators (`~`) over wildcard operators (`*`), and caret operators (`^`) over tilde operators (`~`) ([#756]), by [@fredden]

## [`4.1.0`][4.1.0]

For a full diff see [`4.0.2...4.1.0`][4.0.2...4.1.0].

### Changed

- Adjusted `Vendor\Composer\VersionConstraintNormalizer` to remove extra spaces in inline aliases ([#889]), by [@fredden]
- Adjusted `Vendor\Composer\VersionConstraintNormalizer` to remove useless inline aliases ([#889]), by [@fredden]

### Fixed

- Adjusted `SchemaNormalizer` to account for objects with string and integer-like properties ([#868]), by [@alexis-saransig-lullabot] and [@fredden]
- Adjusted `SchemaNormalizer` to account for objects where schema describes additional properties ([#873]), by [@fredden] and [@localheinz]

## [`4.0.2`][4.0.2]

For a full diff see [`4.0.1...4.0.2`][4.0.1...4.0.2].

### Fixed

- Adjusted `Vendor\Composer\VersionConstraintNormalizer` to sort versions naturally ([#863]), by [@localheinz]

## [`4.0.1`][4.0.1]

For a full diff see [`4.0.0...4.0.1`][4.0.0...4.0.1].

### Fixed

- Adjusted `Vendor\Composer\ComposerJsonNormalizer` to stop sorting `repositories` ([#858]), by [@localheinz]
- Reverted inlining `Vendor\Composer\BinNormalizer` ([#860]), by [@localheinz]
- Partially reverted removal of `Vendor\Composer\ConfigHashNormalizer` to ensure `config` is sorted by ket ([#861]), by [@localheinz]

## [`4.0.0`][4.0.0]

For a full diff see [`3.0.0...4.0.0`][3.0.0...4.0.0].

### Added

- Added `FormatNormalizer` ([#781]), by [@localheinz]

### Changed

- Dropped support for PHP 7.4 ([#757]), by [@localheinz]
- Required `ergebnis/json-schema-validator:^4.0.0` ([#771]), by [@localheinz]
- Allowed configuring the `Normalizer\SchemaNormalizer` to exclude properties from being sorted ([#774]), by [@localheinz]
- Adjusted `Vendor\Composer\BinNormalizer`, `Vendor\Composer\PackageHashNormalizer`, `Vendor\Composer\VersionConstraintNormalizer`, and `SchemaNormalizer` to encode JSON with `JSON_PRETTY_PRINT` flag ([#795]), by [@localheinz]
- Adjusted `Vendor\Composer\BinNormalizer`, `Vendor\Composer\PackageHashNormalizer`, `Vendor\Composer\VersionConstraintNormalizer`, and `SchemaNormalizer` to encode JSON with `JSON_UNESCAPED_SLASHES` flag ([#801]), by [@localheinz]
- Adjusted `Vendor\Composer\BinNormalizer`, `Vendor\Composer\PackageHashNormalizer`, `Vendor\Composer\VersionConstraintNormalizer`, and `SchemaNormalizer` to encode JSON with `JSON_UNESCAPED_UNICODE` flag ([#802]), by [@localheinz]
- Adjusted `Vendor\Composer\ComposerJsonNormalizer` to reject JSON when it is not an object ([#804]), by [@localheinz]
- Adjusted `Vendor\Composer\ComposerJsonNormalizer` to compose `WithFinalNewLineNormalizer` ([#806]), by [@localheinz]
- Adjusted `Vendor\Composer\VersionConstraintNormalizer` to skip normalization of version constraints when they can not be parsed by `Composer\Semver\VersionParser` ([#813]), by [@fredden] and [@localheinz]
- Adjusted `Vendor\Composer\VersionConstraintNormalizer` to sort versions in ascending order ([#816]), by [@fredden]
- Adjusted `Vendor\Composer\VersionConstraintNormalizer` to normalize version constraints separators in `and` constraints from space (` `) or comma (`,`) to space (` `) ([#819]), by [@fredden]
- Adjusted `Vendor\Composer\VersionConstraintNormalizer` to remove overlapping version constraints in `or` version constraints ([#850]), by [@fredden]
- Adjusted `Vendor\Composer\VersionConstraintNormalizer` to remove duplicate version constraints ([#856]), by [@localheinz]

### Fixed

- Adjusted `Vendor\Composer\ComposerJsonNormalizer` to stop sorting `scripts.auto-scripts` ([#776]), by [@localheinz]
- Adjusted `Vendor\Composer\ComposerJsonNormalizer` to stop sorting `extra.installer-paths` ([#777]), by [@localheinz]
- Adjusted `Vendor\Composer\ComposerJsonNormalizer` to stop sorting `config.allow-plugins` ([#778]), by [@localheinz]
- Adjusted `Vendor\Composer\ComposerJsonNormalizer` to stop sorting `config.preferred-install` ([#779]), by [@localheinz]
- Adjusted `Vendor\Composer\ComposerJsonNormalizer` to stop sorting children of `extra.patches` ([#780]), by [@localheinz]

### Removed

- Started using `ergebnis/json` and removed `Json` and `Exception\InvalidJsonEncoded` ([#772]), by [@localheinz]
- Removed `Vendor\Composer\ConfigHashNormalizer` ([#775]), by [@localheinz]
- Removed `AutoFormatNormalizer` ([#793]), by [@localheinz]
- Removed `FixedFormatNormalizer` ([#794]), by [@localheinz]
- Inlined and removed `Vendor\Composer\BinNormalizer` ([#805]), by [@localheinz]

## [`3.0.0`][3.0.0]

For a full diff see [`2.2.0...3.0.0`][2.2.0...3.0.0].

### Changed

- Required `ergebnis/json-schema-validator:^3.0.0` ([#666]), by [@dependabot]
- Renamed `Exception\ExceptionInterface` to `Exception\Exception` ([#667]), by [@localheinz]
- Removed `Exception` suffix from all exceptions ([#668]), by [@localheinz]
- Renamed `NormalizerInterface` to `Normalizer` ([#669]), by [@localheinz]
- Renamed `Format\Formatter` to `Format\DefaultFormatter` ([#672]), by [@localheinz]
- Renamed `Format\FormatterInterface` to `Format\Formatter` ([#673]), by [@localheinz]
- Required `ergebnis/json-pointer:^3.0.0` and `ergebnis/json-schema-validator:^3.1.0` ([#697]), by [@localheinz]
- Required `ergebnis/json-pointer:^3.1.0` ([#698]), by [@dependabot]
- Required `justinrainbow/json-schema:^5.2.12` ([#705]), by [@dependabot]

### Fixed

- Adjusted `ConfigHashNormalizer` to sort keys correctly ([#723]), by [@fredded]

## [`2.2.0`][2.2.0]

For a full diff see [`2.1.0...2.2.0`][2.1.0...2.2.0].

### Changed

- Stopped checking whether `type` property in schema is set to `array` or `object` ([#632]), by [@localheinz]
- Adjusted `SchemaNormalizer` to normalize additional object properties ([#639]), by [@localheinz]
- Adjusted `SchemaNormalizer` to normalize array values for which schema does not declare item schema ([#641]), by [@localheinz]

## [`2.1.0`][2.1.0]

For a full diff see [`2.0.0...2.1.0`][2.0.0...2.1.0].

### Changed

- Adjusted `SchemaNormalizer` to support `anyOf` ([#623]), by [@localheinz]

## [`2.0.0`][2.0.0]

For a full diff see [`1.0.3...2.0.0`][1.0.3...2.0.0].

### Changed

- Dropped support for PHP 7.2 ([#564]), by [@localheinz]
- Dropped support for PHP 7.3 ([#573]), by [@localheinz]
- Renamed `Format::__toString()`, `Indent::__toString()`, and `Json::__toString()` to `Format::toString()`, `Indent::toString()`, and `Json::toString()`, requiring consumers to explicitly invoke methods instead of allowing to cast to `string` ([#589]), by [@localheinz]
- Started using the `SchemaValidator` provided by `ergebnis/json-schema-validator` ([#595]), by [@localheinz]
- Renamed `Format\JsonEncodeOptions::value()` to `Format\JsonEncodeOptions::toInt()` ([#603]), by [@localheinz]
- Extracted `Format\Format::create()` as named constructor and reduced visibility of `__construct`  to `private` ([#608]), by [@localheinz]
- Stopped composing `Format\Format` into `Json` ([#616]), by [@localheinz]
- Renamed `FinalNewLineNormalizer` to `WithFinalNewLineNormalizer` ([#618]), by [@localheinz]
- Renamed `NoFinalNewLineNormalizer` to `WithoutFinalNewLineNormalizer` ([#619]), by [@localheinz]

### Fixed

- Updated `justinrainbow/json-schema` ([#517]), by [@dependabot]
- Stopped sorting the newly added `allow-plugins` configuration ([#590]), by [@dependabot]

### Removed

- Removed `Validator\Result`, `Valdiator\SchemaValidator`, and `Validator\SchemaValidatorInterface` ([#597]), by [@dependabot]

## [`1.0.3`][1.0.3]

For a full diff see [`1.0.2...1.0.3`][1.0.2...1.0.3].

### Fixed

- Adjusted `Vendor\Composer\PackageHashNormalizer` to take into account the newly addded `composer-plugin-api` as platform requirement ([#463]), by [@localheinz]

## [`1.0.2`][1.0.2]

For a full diff see [`1.0.1...1.0.2`][1.0.1...1.0.2].

### Fixed

- Adjusted `Vendor\Composer\ConfigHashNormalizer` to take into account the full property path, not only the property name ([#429]), by [@localheinz]

## [`1.0.1`][1.0.1]

For a full diff see [`1.0.0...1.0.1`][1.0.0...1.0.1].

### Fixed

- Adjusted `Vendor\Composer\ConfigHashNormalizer` to ignore the `preferred-install` hash ([#425]), by [@localheinz]

## [`1.0.0`][1.0.0]

For a full diff see [`0.14.1...1.0.0`][0.14.1...1.0.0].

### Changed

- Adjusted `Vendor\Composer\ConfigHashNormalizer` to recursively sort hashes by key ([#424]), by [@localheinz]

## [`0.14.1`][0.14.1]

For a full diff see [`0.14.0...0.14.1`][0.14.0...0.14.1].

### Fixed

- Adjusted `Vendor\Composer\ConfigHashNormalizer` to continue normalizing properties when a property has an empty value ([#423]), by [@localheinz]

## [`0.14.0`][0.14.0]

For a full diff see [`0.13.1...0.14.0`][0.13.1...0.14.0].

### Added

- Extracted an `Indent::CHARACTERS` constant that exposes a map of indent styles to indent characters ([#384]), by [@localheinz]

## [`0.13.1`][0.13.1]

For a full diff see [`0.13.0...0.13.1`][0.13.0...0.13.1].

### Changed

- Dropped support for PHP 7.1 ([#335]), by [@localheinz]

## [`0.13.0`][0.13.0]

For a full diff see [`0.12.0...0.13.0`][0.12.0...0.13.0].

### Added

- Added support for PHP 8.0 ([#308]), by [@localheinz]

## [`0.12.0`][0.12.0]

For a full diff see [`0.11.0...0.12.0`][0.11.0...0.12.0].

### Added

- Added `SchemaValidator::validate()`, which returns a `Result` composing validation error messages ([#268]), by [@localheinz]

### Deprecated

- Deprecated `SchemaValidator::isValid()` ([#269]), by [@localheinz]

## [`0.11.0`][0.11.0]

For a full diff see [`0.10.1...0.11.0`][0.10.1...0.11.0].

### Added

- Merged in normalizers from [`ergebnis/composer-json-normalizer`](https://github.com/ergebnis/composer-json-normalizer) ([#203]), by [@localheinz]

### Removed

- Removed the `ChainUriRetriever` ([#202]), by [@localheinz]

## [`0.10.1`][0.10.1]

For a full diff see [`0.10.0...0.10.1`][0.10.0...0.10.1].

### Fixed

- Brought back support for PHP 7.1 ([#191]), by [@localheinz]

## [`0.10.0`][0.10.0]

For a full diff see [`0.9.0...0.10.0`][0.9.0...0.10.0].

### Added

- Added a `ChainUriRetriever` which allows specifying multiple URI retrievers ([#102]), by [@localheinz]
- Added this changelog ([#103]), by [@localheinz]

### Changed

- Allowing injection of a `UriRetriever` into the `SchemaNormalizer`, and defaulting to a `ChainUriRetriever` which composes `FileGetContents` and `Curl` URI retrievers ([#104]), by [@localheinz]
- Dropped `null` default values of constructor arguments of `AutoFormatNormalizer`, `FixedFormatNormalizer`, `Formatter`, `IndentNormalizer` to expose hard dependencies ([#109]), by [@localheinz]
- Dropped nullable return type declaration from `ChainUriRetriever::getContentType()`, defaulting to an empty `string` when `ChainUriRetriever::retrieve()` wasn't invoked yet ([#132]), by [@localheinz]
- Started using `ergebnis/json-printer` instead of `localheinz/json-printer` ([#176]), by [@localheinz]
- Renamed vendor namespace `Localheinz` to `Ergebnis` after move to [@ergebnis] ([#181]), by [@localheinz]

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

- Dropped support for PHP 7.1 ([#163]), by [@localheinz]

## [`0.9.0`][0.9.0]

For a full diff see [`0.8.0...0.9.0`][0.8.0...0.9.0].

### Added

- Added `JsonEncodeOptions` value object ([#93]), by [@localheinz]

### Changed

- Turned method on `Format` into named constructor on `Indent` value object ([#94]), by [@localheinz]
- Turned method on `Format` into named constructor on `JsonEncodeOptions` value object([#95]), by [@localheinz]
- Turned method on `Format` into named constructor on `NewLine` value object ([#96]), by [@localheinz]

### Removed

- Removed capability to create `Json` value object from decoded data ([#88]), by [@localheinz]
- Removed `IndentInterface` ([#89]), by [@localheinz]
- Removed `NewLineInterface` ([#90]), by [@localheinz]
- Removed `FormatInterface` ([#91]), by [@localheinz]
- Removed `JsonInterface` ([#92]), by [@localheinz]

## [`0.8.0`][0.8.0]

For a full diff see [`0.7.0...0.8.0`][0.7.0...0.8.0].

### Added

- Added named constructor to `Json` value object to allow creation from data ([#86]), by [@localheinz]

### Changed

- Renamed `InvalidJsonException` to `InvalidJsonEncodedException` ([#85]), by [@localheinz]

### Fixed

- `ExceptionInterface` now extends `Throwable` ([#82]), by [@BackEndTea]
- Extension `ext/json` is now explicitly required  ([#84]), by [@localheinz]

## [`0.7.0`][0.7.0]

For a full diff see [`0.6.0...0.7.0`][0.6.0...0.7.0].

### Added

- Added `Indent` value object ([#73]), by [@localheinz]
- Added `NewLine` value object ([#76]), by [@localheinz]
- Added `Json` value object ([#64]), by [@localheinz]
- Added exceptions ([#79]), by [@localheinz]

### Changed

- Removed the `Sniffer` in favour of a named constructor on `Format` value object ([#77]), by [@localheinz]

### Fixed

- Added more test cases for sniffing JSON without whitespace ([#67]), by [@localheinz]
- Added missing types in a Docblock ([#68]), by [@localheinz]
- The `Format` value object now rejects mixed tabs and spaces as indent ([#69]), by [@localheinz]
- Added more test cases for JSON without indent ([#72]), by [@localheinz]
- Sniff only pure indents, no mixed spaces and tabs ([#71]), by [@localheinz]

## [`0.6.0`][0.6.0]

For a full diff see [`0.5.2...0.6.0`][0.5.2...0.6.0].

### Added

- Added sniffing of new-line character sequence ([#55]), by [@localheinz]

## [`0.5.2`][0.5.2]

For a full diff see [`0.5.1...0.5.2`][0.5.1...0.5.2].

### Fixed

- Keep resolving references until there are none left ([#49]), by [@localheinz]

## [`0.5.1`][0.5.1]

For a full diff see [`0.5.0...0.5.1`][0.5.0...0.5.1].

### Fixed

- Resolve referenced schema in `oneOf` combination ([#47]), by [@localheinz]

## [`0.5.0`][0.5.0]

For a full diff see [`0.4.0...0.5.0`][0.4.0...0.5.0].

### Added

- Added handling of arrays where schema describes tuple ([#37]), by [@localheinz]
- Added handling of arrays were schema has reference definition ([#40]), by [@localheinz]
- Added handling of `oneOf` ([#45]), by [@localheinz]

## [`0.4.0`][0.4.0]

For a full diff see [`0.3.0...0.4.0`][0.3.0...0.4.0].

### Added

- Extracted `Formatter` ([#31]), by [@localheinz]

### Changed

- Renamed `FormatSniffer` to `Sniffer` ([#30]), by [@localheinz]

## [`0.3.0`][0.3.0]

For a full diff see [`0.2.0...0.3.0`][0.2.0...0.3.0].

### Changed

- Require PHP 7.1 ([#27]), by [@localheinz]
- Allow to mutate `Format` value object ([#29]), by [@localheinz]

## [`0.2.0`][0.2.0]

For a full diff see [`0.1.0...0.2.0`][0.1.0...0.2.0].

### Added

- Added `FixedFormatNormalizer` ([#17]), by [@localheinz]

## [`0.1.0`][0.1.0]

For a full diff see [`5d8b3e2...0.1.0`][5d8b3e2...0.1.0].

### Added

- Added `IndentNormalizer` ([#1]), by [@localheinz]
- Added `FinalNewLineNormalizer` ([#2]), by [@localheinz]
- Added `NoFinalNewLineNormalizer` ([#3]), by [@localheinz]
- Added `JsonEncodeNormalizer` ([#7]), by [@localheinz]
- Added `CallableNormalizer` ([#8]), by [@localheinz]
- Added `ChainNormalizer` ([#9]), by [@localheinz]
- Added `Format` value object ([#10]), by [@localheinz]
- Added `FormatSniffer` ([#12]), by [@localheinz]
- Added `AutoFormatNormalizer` ([#13]), by [@localheinz]
- Added `SchemaNormalizer` ([#15]), by [@localheinz]

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
[1.0.3]: https://github.com/ergebnis/json-normalizer/releases/tag/1.0.3
[2.0.0]: https://github.com/ergebnis/json-normalizer/releases/tag/2.0.0
[2.1.0]: https://github.com/ergebnis/json-normalizer/releases/tag/2.1.0
[2.2.0]: https://github.com/ergebnis/json-normalizer/releases/tag/2.2.0
[3.0.0]: https://github.com/ergebnis/json-normalizer/releases/tag/3.0.0
[4.0.0]: https://github.com/ergebnis/json-normalizer/releases/tag/4.0.0
[4.0.1]: https://github.com/ergebnis/json-normalizer/releases/tag/4.0.1
[4.0.2]: https://github.com/ergebnis/json-normalizer/releases/tag/4.0.2
[4.1.0]: https://github.com/ergebnis/json-normalizer/releases/tag/4.1.0
[4.2.0]: https://github.com/ergebnis/json-normalizer/releases/tag/4.2.0
[4.3.0]: https://github.com/ergebnis/json-normalizer/releases/tag/4.3.0
[4.4.0]: https://github.com/ergebnis/json-normalizer/releases/tag/4.4.0
[4.4.1]: https://github.com/ergebnis/json-normalizer/releases/tag/4.4.1
[4.5.0]: https://github.com/ergebnis/json-normalizer/releases/tag/4.5.0

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
[1.0.3...2.0.0]: https://github.com/ergebnis/json-normalizer/compare/1.0.3...2.0.0
[2.0.0...2.1.0]: https://github.com/ergebnis/json-normalizer/compare/2.0.0...2.1.0
[2.1.0...2.2.0]: https://github.com/ergebnis/json-normalizer/compare/2.1.0...2.2.0
[2.2.0...3.0.0]: https://github.com/ergebnis/json-normalizer/compare/2.2.0...3.0.0
[3.0.0...4.0.0]: https://github.com/ergebnis/json-normalizer/compare/3.0.0...4.0.0
[4.0.0...4.0.1]: https://github.com/ergebnis/json-normalizer/compare/4.0.0...4.0.1
[4.0.1...4.0.2]: https://github.com/ergebnis/json-normalizer/compare/4.0.1...4.0.2
[4.0.2...4.1.0]: https://github.com/ergebnis/json-normalizer/compare/4.0.2...4.1.0
[4.1.0...4.2.0]: https://github.com/ergebnis/json-normalizer/compare/4.1.0...4.2.0
[4.2.0...4.3.0]: https://github.com/ergebnis/json-normalizer/compare/4.2.0...4.3.0
[4.3.0...4.4.0]: https://github.com/ergebnis/json-normalizer/compare/4.4.0...main
[4.4.0...4.4.1]: https://github.com/ergebnis/json-normalizer/compare/4.4.0...4.4.1
[4.4.1...4.5.0]: https://github.com/ergebnis/json-normalizer/compare/4.4.1...4.5.0
[4.5.0...main]: https://github.com/ergebnis/json-normalizer/compare/4.5.0...main

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
[#517]: https://github.com/ergebnis/json-normalizer/pull/517
[#564]: https://github.com/ergebnis/json-normalizer/pull/564
[#573]: https://github.com/ergebnis/json-normalizer/pull/573
[#589]: https://github.com/ergebnis/json-normalizer/pull/589
[#590]: https://github.com/ergebnis/json-normalizer/pull/590
[#595]: https://github.com/ergebnis/json-normalizer/pull/595
[#597]: https://github.com/ergebnis/json-normalizer/pull/597
[#603]: https://github.com/ergebnis/json-normalizer/pull/603
[#608]: https://github.com/ergebnis/json-normalizer/pull/608
[#616]: https://github.com/ergebnis/json-normalizer/pull/616
[#618]: https://github.com/ergebnis/json-normalizer/pull/618
[#619]: https://github.com/ergebnis/json-normalizer/pull/619
[#623]: https://github.com/ergebnis/json-normalizer/pull/623
[#632]: https://github.com/ergebnis/json-normalizer/pull/632
[#639]: https://github.com/ergebnis/json-normalizer/pull/639
[#641]: https://github.com/ergebnis/json-normalizer/pull/641
[#666]: https://github.com/ergebnis/json-normalizer/pull/666
[#667]: https://github.com/ergebnis/json-normalizer/pull/667
[#668]: https://github.com/ergebnis/json-normalizer/pull/668
[#669]: https://github.com/ergebnis/json-normalizer/pull/669
[#672]: https://github.com/ergebnis/json-normalizer/pull/672
[#673]: https://github.com/ergebnis/json-normalizer/pull/673
[#697]: https://github.com/ergebnis/json-normalizer/pull/697
[#698]: https://github.com/ergebnis/json-normalizer/pull/698
[#705]: https://github.com/ergebnis/json-normalizer/pull/705
[#723]: https://github.com/ergebnis/json-normalizer/pull/723
[#756]: https://github.com/ergebnis/json-normalizer/pull/756
[#757]: https://github.com/ergebnis/json-normalizer/pull/757
[#772]: https://github.com/ergebnis/json-normalizer/pull/772
[#774]: https://github.com/ergebnis/json-normalizer/pull/774
[#775]: https://github.com/ergebnis/json-normalizer/pull/775
[#776]: https://github.com/ergebnis/json-normalizer/pull/776
[#777]: https://github.com/ergebnis/json-normalizer/pull/777
[#778]: https://github.com/ergebnis/json-normalizer/pull/778
[#779]: https://github.com/ergebnis/json-normalizer/pull/779
[#780]: https://github.com/ergebnis/json-normalizer/pull/780
[#781]: https://github.com/ergebnis/json-normalizer/pull/781
[#793]: https://github.com/ergebnis/json-normalizer/pull/793
[#794]: https://github.com/ergebnis/json-normalizer/pull/794
[#795]: https://github.com/ergebnis/json-normalizer/pull/795
[#801]: https://github.com/ergebnis/json-normalizer/pull/801
[#802]: https://github.com/ergebnis/json-normalizer/pull/802
[#804]: https://github.com/ergebnis/json-normalizer/pull/804
[#805]: https://github.com/ergebnis/json-normalizer/pull/805
[#813]: https://github.com/ergebnis/json-normalizer/pull/813
[#816]: https://github.com/ergebnis/json-normalizer/pull/816
[#819]: https://github.com/ergebnis/json-normalizer/pull/819
[#850]: https://github.com/ergebnis/json-normalizer/pull/850
[#856]: https://github.com/ergebnis/json-normalizer/pull/856
[#858]: https://github.com/ergebnis/json-normalizer/pull/858
[#860]: https://github.com/ergebnis/json-normalizer/pull/860
[#861]: https://github.com/ergebnis/json-normalizer/pull/861
[#863]: https://github.com/ergebnis/json-normalizer/pull/863
[#868]: https://github.com/ergebnis/json-normalizer/pull/868
[#873]: https://github.com/ergebnis/json-normalizer/pull/873
[#889]: https://github.com/ergebnis/json-normalizer/pull/889
[#917]: https://github.com/ergebnis/json-normalizer/pull/917
[#980]: https://github.com/ergebnis/json-normalizer/pull/980
[#982]: https://github.com/ergebnis/json-normalizer/pull/982
[#988]: https://github.com/ergebnis/json-normalizer/pull/988
[#989]: https://github.com/ergebnis/json-normalizer/pull/989
[#991]: https://github.com/ergebnis/json-normalizer/pull/991
[#992]: https://github.com/ergebnis/json-normalizer/pull/992
[#1001]: https://github.com/ergebnis/json-normalizer/pull/1001
[#1027]: https://github.com/ergebnis/json-normalizer/pull/1027
[#1039]: https://github.com/ergebnis/json-normalizer/pull/1039
[#1073]: https://github.com/ergebnis/json-normalizer/pull/1073
[#1074]: https://github.com/ergebnis/json-normalizer/pull/1074
[#1075]: https://github.com/ergebnis/json-normalizer/pull/1075
[#1076]: https://github.com/ergebnis/json-normalizer/pull/1076
[#1077]: https://github.com/ergebnis/json-normalizer/pull/1077
[#1079]: https://github.com/ergebnis/json-normalizer/pull/1079

[@alexis-saransig-lullabot]: https://github.com/alexis-saransig-lullabot
[@BackEndTea]: https://github.com/BackEndTea
[@dependabot]: https://github.com/dependabot
[@ergebnis]: https://github.com/ergebnis
[@fredden]: https://github.com/fredden
[@localheinz]: https://github.com/localheinz
