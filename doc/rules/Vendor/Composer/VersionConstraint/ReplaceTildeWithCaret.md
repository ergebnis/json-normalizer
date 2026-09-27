# `vendor/composer/version-constraint/replace-tilde-with-caret`

Replaces version ranges with a tilde with version ranges with a caret in version constraints where they are equivalent.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\ReplaceTildeWithCaret`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\VersionConstraint\ReplaceTildeWithCaret::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json-normalizer": "~4.1"
+        "ergebnis/json-normalizer": "^4.1"
     }
 }
```
