# jsonc

PHP JSON with Comments(Cwola library).

## Overview

Providing parser of JSONC(JSON with Comments) for PHP.

## Requirement
- PHP8.0+

## Usage
```
<?php

$value = <<< JSONC
/**
 * JSON with Comments for PHP.
 * DOC BLOCK.
 */

// Line comment.

{
    "id": "xxx",  // Identify
    "name": "jhon doe"  // your name
    "age": 0,
    "keyword": [
        "xxx", 1, -5
    ]
}
JSONC;

$json = Cwola\Jsonc\decode($value);
echo $json['name'];  // jhon doe

echo Cwola\Jsonc\toJson($value);  // output json string

echo Cwola\Jsonc\toXml($value);  // output xml string

echo Cwola\Jsonc\toReadableAST($value);  // output Abstract Syntax Tree
```

## Licence

[MIT](https://github.com/cwola/jsonc/blob/main/LICENSE)
