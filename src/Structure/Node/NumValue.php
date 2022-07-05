<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Structure\Node;

class NumValue extends Value {
    /**
     * @param int|float $value
     */
    public function __construct(int|float $value) {
        $this->value = $value;
    }
}
