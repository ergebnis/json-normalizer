# `vendor/composer/config/sort-properties-with-wildcards`

Sorts the properties of `config.allow-plugins` and `config.preferred-install` by name, with a wildcard after every other character, unless a name has a wildcard other than at its end.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Config\SortPropertiesWithWildcards`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\Config\SortPropertiesWithWildcards::create());
```

#### Changes

```diff
 {
     "config": {
         "allow-plugins": {
-            "foo/*": true,
             "bar/baz": true,
-            "foo/bar": false
+            "foo/bar": false,
+            "foo/*": true
         }
     }
 }
```
