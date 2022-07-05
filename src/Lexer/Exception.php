<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Lexer;

use Cwola\Jsonc\Exception\JsoncException;

class Exception extends JsoncException {
    const CODE = 300;
    public function __construct(string $message = 'Lexer error.') {
        parent::__construct($message, static::CODE);
    }
}
