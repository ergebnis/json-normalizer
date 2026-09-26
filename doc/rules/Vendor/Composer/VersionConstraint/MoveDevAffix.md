# `vendor/composer/version-constraint/move-dev-affix`

Moves `dev` to the end of numeric branch names and to the start of other branch names in version constraints.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\MoveDevAffix`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\VersionConstraint\MoveDevAffix::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json-normalizer": "main-dev"
+        "ergebnis/json-normalizer": "dev-main"
     }
 }
```
