# `vendor/composer/version-constraint/remove-overlapping`

Removes or-constraints that other or-constraints with a caret or a tilde already cover from version constraints.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\RemoveOverlapping`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\VersionConstraint\RemoveOverlapping::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json-normalizer": "^4.0 || ^4.1"
+        "ergebnis/json-normalizer": "^4.0"
     }
 }
```
