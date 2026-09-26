# `vendor/composer/bin/sort-elements`

Sorts the elements of `bin` by value.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\Bin\SortElements`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\Bin\SortElements::create());
```

#### Changes

```diff
 {
     "bin": [
-        "bin/b",
-        "bin/a"
+        "bin/a",
+        "bin/b"
     ]
 }
```
