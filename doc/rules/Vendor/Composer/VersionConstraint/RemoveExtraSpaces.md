# `vendor/composer/version-constraint/remove-extra-spaces`

Replaces consecutive spaces in version constraints with a single space.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\RemoveExtraSpaces`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\VersionConstraint\RemoveExtraSpaces::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json-normalizer": "^4.0  ||  ^5.0"
+        "ergebnis/json-normalizer": "^4.0 || ^5.0"
     }
 }
```
