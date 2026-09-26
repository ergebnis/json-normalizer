# `vendor/composer/version-constraint/remove-duplicates`

Removes duplicate or-constraints and and-constraints from version constraints.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\RemoveDuplicates`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\VersionConstraint\RemoveDuplicates::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json-normalizer": "^4.0 || ^4.0"
+        "ergebnis/json-normalizer": "^4.0"
     }
 }
```
