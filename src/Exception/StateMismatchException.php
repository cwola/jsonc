<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

class StateMismatchException extends JsoncException {
    const CODE = JSON_ERROR_STATE_MISMATCH;
    public function __construct(string $message = 'An underflow or mode mismatch has occurred.') {
        parent::__construct($message, static::CODE);
    }
}
