# `vendor/composer/version-constraint/sort`

Sorts or-constraints and and-constraints in version constraints by version.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\Sort`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\VersionConstraint\Sort::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json-normalizer": "^5.0 || ^4.0"
+        "ergebnis/json-normalizer": "^4.0 || ^5.0"
     }
 }
```
