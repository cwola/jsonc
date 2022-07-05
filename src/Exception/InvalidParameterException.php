<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

class InvalidParameterException extends JsoncException {
    const CODE = 100;
    public function __construct(string $message = 'Invalid parameter.') {
        parent::__construct($message, static::CODE);
    }
}
