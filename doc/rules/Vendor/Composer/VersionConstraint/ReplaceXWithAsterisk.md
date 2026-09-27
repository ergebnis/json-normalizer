# `vendor/composer/version-constraint/replace-x-with-asterisk`

Replaces the wildcard `x` with `*` in version constraints.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\ReplaceXWithAsterisk`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\VersionConstraint\ReplaceXWithAsterisk::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json-normalizer": "4.x"
+        "ergebnis/json-normalizer": "4.*"
     }
 }
```
