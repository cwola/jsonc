<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

class InfOrNanException extends JsoncException {
    const CODE = JSON_ERROR_INF_OR_NAN;
    public function __construct(string $message = 'The value passed to json_encode() contains an INF or NAN.') {
        parent::__construct($message, static::CODE);
    }
}
