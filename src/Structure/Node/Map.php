<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Structure\Node;

class Map extends AbstractNode {
    /**
     * {@inheritDoc}
     */
    public function isAppendable(AbstractNode $node) :bool {
        return ($node instanceof Comment)
                || ($node instanceof Key);
    }
}
