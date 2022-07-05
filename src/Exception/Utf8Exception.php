<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

class Utf8Exception extends JsoncException {
    const CODE = JSON_ERROR_UTF8;
    public function __construct(string $message = 'UTF-8 characters in an incorrect format, such as not being encoded correctly.') {
        parent::__construct($message, static::CODE);
    }
}
