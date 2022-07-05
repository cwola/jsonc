<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Structure\Node;

abstract class Value extends AbstractNode {
    /**
     * {@inheritDoc}
     */
    public function isAppendable(AbstractNode $node) :bool {
        return false;
    }
}
