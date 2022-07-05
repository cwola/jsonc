<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Structure\Node;

class BoolValue extends Value {
    /**
     * @param bool $value
     */
    public function __construct(bool $value) {
        $this->value = $value;
    }
}
