<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

class CtrlCharException extends JsoncException {
    const CODE = JSON_ERROR_CTRL_CHAR;
    public function __construct(string $message = 'Control character error.') {
        parent::__construct($message, static::CODE);
    }
}
