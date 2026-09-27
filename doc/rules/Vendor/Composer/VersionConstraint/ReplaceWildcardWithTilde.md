# `vendor/composer/version-constraint/replace-wildcard-with-tilde`

Replaces version ranges with a wildcard with version ranges with a tilde in version constraints.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\ReplaceWildcardWithTilde`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\VersionConstraint\ReplaceWildcardWithTilde::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json-normalizer": "4.1.*"
+        "ergebnis/json-normalizer": "~4.1.0"
     }
 }
```
