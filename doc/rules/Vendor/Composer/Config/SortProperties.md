# `vendor/composer/config/sort-properties`

Sorts the properties of `config` by name.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Config\SortProperties`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\Config\SortProperties::create());
```

#### Changes

```diff
 {
     "config": {
-        "sort-packages": true,
+        "allow-plugins": {
+            "ergebnis/composer-normalize": true
+        },
         "platform": {
             "php": "7.4.33"
         },
-        "allow-plugins": {
-            "ergebnis/composer-normalize": true
-        }
+        "sort-packages": true
     }
 }
```
