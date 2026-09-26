# `sort/properties-by-schema`

Sorts the properties of objects in the order in which the schema lists them, and keeps properties that the schema does not list after them, in their order.

Class: `Ergebnis\Json\Normalizer\Rule\Sort\PropertiesBySchema`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()
    ->withRules(Normalizer\Rule\Sort\PropertiesBySchema::create())
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
        },
        "license": {
            "type": "string"
        }
    }
}
```

#### Changes

```diff
 {
+    "name": "ergebnis/json-normalizer",
     "license": "MIT",
-    "type": "library",
-    "name": "ergebnis/json-normalizer"
+    "type": "library"
 }
```
