<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

class SyntaxException extends JsoncException {
    const CODE = JSON_ERROR_SYNTAX;
    public function __construct(string $message = 'Syntax error.') {
        parent::__construct($message, static::CODE);
    }
}
