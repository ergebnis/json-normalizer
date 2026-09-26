# `vendor/composer/version-constraint/trim`

Removes whitespace around version constraints.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\Trim`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\VersionConstraint\Trim::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json-normalizer": " ^4.0 "
+        "ergebnis/json-normalizer": "^4.0"
     }
 }
```
