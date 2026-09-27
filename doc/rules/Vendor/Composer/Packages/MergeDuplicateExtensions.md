# `vendor/composer/packages/merge-duplicate-extensions`

Renames extensions in package links to lower case with spaces replaced by hyphens, and merges the version constraints of extensions that then have the same name.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Packages\MergeDuplicateExtensions`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\Packages\MergeDuplicateExtensions::create());
```

#### Changes

```diff
 {
     "require": {
-        "ext-json": "^1.0",
-        "php": "^8.0",
-        "ext-JSON": "^2.0"
+        "ext-json": "^2.0||^1.0",
+        "php": "^8.0"
     }
 }
```
