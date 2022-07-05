<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

class Utf16Exception extends JsoncException {
    const CODE = JSON_ERROR_UTF16;
    public function __construct(string $message = 'The JSON string passed to json_decode() contained a single, unpaired UTF-16 surrogate code point.') {
        parent::__construct($message, static::CODE);
    }
}
