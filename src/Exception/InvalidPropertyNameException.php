<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

class InvalidPropertyNameException extends JsoncException {
    const CODE = JSON_ERROR_INVALID_PROPERTY_NAME;
    public function __construct(string $message = 'When decoding a JSON object into a PHP object, the string passed to json_decode() contained a key that started with \u0000.') {
        parent::__construct($message, static::CODE);
    }
}
