<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Structure\Node;

class StrValue extends Value {
    /**
     * @param string $value
     */
    public function __construct(string $value) {
        $this->value = $value;
    }
}
