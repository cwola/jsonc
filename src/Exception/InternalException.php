<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

class InternalException extends JsoncException {
    const CODE = 999;
    public function __construct(string $message = 'Internal error.') {
        parent::__construct($message, static::CODE);
    }
}
