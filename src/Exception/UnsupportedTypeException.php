<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

class UnsupportedTypeException extends JsoncException {
    const CODE = JSON_ERROR_UNSUPPORTED_TYPE;
    public function __construct(string $message = 'A type that is not supported by json_encode(), such as resource, was passed.') {
        parent::__construct($message, static::CODE);
    }
}
