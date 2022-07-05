<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Structure\Node;

class Set extends AbstractNode {
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
