# `vendor/composer/version-constraint/remove-useless-inline-aliases`

Removes inline aliases that alias a version to itself from version constraints.

Class: `Ergebnis\Json\Normalizer\Rule\Vendor\Composer\VersionConstraint\RemoveUselessInlineAliases`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()->withRules(Normalizer\Rule\Vendor\Composer\VersionConstraint\RemoveUselessInlineAliases::create());
```

#### Changes

```diff
 {
     "require": {
-        "ergebnis/json-normalizer": "4.1.0 as 4.1.0"
+        "ergebnis/json-normalizer": "4.1.0"
     }
 }
```
