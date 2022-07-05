<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Structure\Node;

class Comment extends AbstractNode {
    /**
     * {@inheritDoc}
     */
    public function isAppendable(AbstractNode $node) :bool {
        return ($node instanceof StrValue);
    }
}
