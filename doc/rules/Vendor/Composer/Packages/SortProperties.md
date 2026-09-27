# `vendor/composer/packages/sort-properties`

Sorts package links with platform packages first, the way Composer sorts them.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Packages\SortProperties`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\Packages\SortProperties::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json": "^1.0",
+        "php": "^8.0",
         "ext-json": "*",
-        "php": "^8.0"
+        "ergebnis/json": "^1.0"
     }
 }
```
