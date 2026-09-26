# `vendor/composer/repositories/sort-filter-elements`

Sorts the elements of `exclude` and `only` of repositories by value, with a wildcard after every other character, unless a value has a wildcard other than at its end.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Repositories\SortFilterElements`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\Repositories\SortFilterElements::create());
```

#### Changes

```diff
 {
     "repositories": [
         {
             "type": "composer",
             "url": "https://packages.example.org",
             "only": [
-                "foo/*",
                 "bar/baz",
-                "foo/bar"
+                "foo/bar",
+                "foo/*"
             ]
         }
     ]
 }
```
