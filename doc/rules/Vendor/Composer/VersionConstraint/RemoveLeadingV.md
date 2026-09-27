# `vendor/composer/version-constraint/remove-leading-v`

Removes the prefix `v` from versions in version constraints.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\RemoveLeadingV`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\VersionConstraint\RemoveLeadingV::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json-normalizer": "^v4.0"
+        "ergebnis/json-normalizer": "^4.0"
     }
 }
```
