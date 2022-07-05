<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Structure\Node;

class NullValue extends Value {
    /**
     * @param void
     */
    public function __construct() {
        $this->value = null;
    }
}
