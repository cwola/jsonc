<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

class DepthException extends JsoncException {
    const CODE = JSON_ERROR_DEPTH;
    public function __construct(string $message = 'Maximum stack depth has been reached.') {
        parent::__construct($message, static::CODE);
    }
}
