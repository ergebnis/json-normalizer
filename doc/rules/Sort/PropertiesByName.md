# `sort/properties-by-name`

Sorts the properties of objects that the schema does not list by name, after the properties that it lists.

Class: `Ergebnis\Json\Normalizer\Rule\Sort\PropertiesByName`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()
    ->withRules(Normalizer\Rule\Sort\PropertiesByName::create())
    ->withSchema(\sprintf(
        'file://%s/schema.json',
        __DIR__,
    ));
```

#### Schema

```json
{
    "type": "object",
    "properties": {
        "name": {
            "type": "string"
        }
    }
}
```

#### Changes

```diff
 {
     "name": "ergebnis/json-normalizer",
-    "type": "library",
-    "license": "MIT"
+    "license": "MIT",
+    "type": "library"
 }
```
