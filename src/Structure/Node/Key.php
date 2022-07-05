<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Structure\Node;

class Key extends AbstractNode {
    /**
     * @param string $value
     */
    public function __construct(string $value) {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function isAppendable(AbstractNode $node) :bool {
        return ($node instanceof Comment)
                || ($node instanceof Map)
                || ($node instanceof Set)
                || ($node instanceof Value);
    }
}
