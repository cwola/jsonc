<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

class RecursionException extends JsoncException {
    const CODE = JSON_ERROR_RECURSION;
    public function __construct(string $message = 'The array or object passed to json_encode() contains a recursive reference and cannot be encoded.') {
        parent::__construct($message, static::CODE);
    }
}
