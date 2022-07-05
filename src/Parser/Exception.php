<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Parser;

use Cwola\Jsonc\Exception\JsoncException;

class Exception extends JsoncException {
    const CODE = 400;
    public function __construct(string $message = 'Parser error.') {
        parent::__construct($message, static::CODE);
    }
}
