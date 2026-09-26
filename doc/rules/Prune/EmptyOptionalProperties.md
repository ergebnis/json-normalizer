# `prune/empty-optional-properties`

Removes properties that the schema lists but does not require when their value is an empty array, an empty object, or null.

Class: `Ergebnis\Json\Normalizer\Rule\Prune\EmptyOptionalProperties`

## Examples

### Example 1

#### Configuration

```php
<?php

declare(strict_types=1);

use Ergebnis\Json\Normalizer;

$configuration = Normalizer\Configuration::create()
    ->withRules(Normalizer\Rule\Prune\EmptyOptionalProperties::create())
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
        "keywords": {
            "type": "array"
        },
        "extra": {
            "type": "object"
        }
    },
    "required": [
        "name"
    ]
}
```

#### Changes

```diff
 {
-    "name": "ergebnis/json-normalizer",
-    "keywords": [],
-    "extra": {}
+    "name": "ergebnis/json-normalizer"
 }
```
