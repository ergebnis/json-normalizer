# `vendor/composer/version-constraint/normalize-separators`

Separates or-constraints with `||` and and-constraints with a space.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\NormalizeSeparators`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\VersionConstraint\NormalizeSeparators::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json-normalizer": ">=4.0,<4.5|^5.0"
+        "ergebnis/json-normalizer": ">=4.0 <4.5 || ^5.0"
     }
 }
```
