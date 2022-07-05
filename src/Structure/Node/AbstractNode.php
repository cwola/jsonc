<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Structure\Node;

use Cwola\Attribute\Readable;
use Cwola\Jsonc\Exception\JsoncException;

abstract class AbstractNode {
    use Readable;

    /**
     * @var \Cwola\Jsonc\Structure\Node\AbstractNode?
     */
    #[Readable]
    protected ?AbstractNode $parent = null;

    /**
     * @var \Cwola\Jsonc\Structure\Node\AbstractNode[]
     */
    #[Readable]
    protected array $children = [];

    /**
     * @var mixed
     */
    #[Readable]
    protected mixed $value = null;


    /**
     * @param \Cwola\Jsonc\Structure\Node\AbstractNode $child
     *
     * @return \Cwola\Jsonc\Structure\Node\AbstractNode $child
     *
     * @throws \Cwola\Jsonc\Exception\JsoncException
     */
    public function appendChild(AbstractNode $child) :AbstractNode {
        if ($this->isAppendable($child)===false) {
            throw new JsoncException('This is a child node that cannot be added.');
        } else if ($this->isSameNode($child) || $child->contains($this)) {
			throw new JsoncException('Node cannot be inserted at the specified point in the hierarchy.');
        }
        $child->parent?->removeChild($child);
        $this->children[$this->sizeOfChildren()] = $child;
        $child->parent = $this;
        return $child;
    }

	/**
	 * @param \Cwola\Jsonc\Structure\Node\AbstractNode $node
	 *
	 * @return bool
	 */
	public function contains(AbstractNode $node) :bool {
		return $this->isSameNode($node)||$this->isAncestorOf($node);
    }

	/**
     * @param void
     *
     * @return bool
	 */
	public function hasChild() :bool {
        return $this->sizeOfChildren()>0;
    }

    /**
     * @param void
     *
     * @return int
     */
    public function sizeOfChildren() :int {
        return \count($this->children);
    }

	/**
     * @param void
     *
     * @return bool
	 */
	public function hasParent() :bool {
        return ($this->parent instanceof AbstractNode);
    }

    /**
     * Is $this the ancestor of $node ?
     *
     * @param \Cwola\Jsonc\Structure\Node\AbstractNode $node
     *
     * @return bool
     */
    public function isAncestorOf(AbstractNode $node) :bool {
        if ($this->hasChild()===false || $node->hasParent()===false || $this->isSameNode($node)) {
            return false;
        }
		while ($node instanceof AbstractNode) {
			if ($this->isParentOf($node)) {
				return true;
			}
			$node = $node->parent;
		}
		return false;
    }

	/**
	 * Is $this the parent of $node ?
     *
     * @param \Cwola\Jsonc\Structure\Node\AbstractNode $node
     *
     * @return bool
	 */
	public function isParentOf(AbstractNode $node) :bool {
        return $node->hasParent()&&$node->parent->isSameNode($this);
    }

	/**
	 * @param \Cwola\Jsonc\Structure\Node\AbstractNode $node
	 *
	 * @return bool
	 */
	public function isSameNode(AbstractNode $node) :bool {
		return $this===$node;
    }

    /**
     * @abstract
     *
     * @param \Cwola\Jsonc\Structure\Node\AbstractNode $node
     *
     * @return bool
     */
    abstract public function isAppendable(AbstractNode $node) :bool;

	/**
	 * @param \Cwola\Jsonc\Structure\Node\AbstractNode $child
	 *
	 * @return \Cwola\Jsonc\Structure\Node\AbstractNode $child
	 *
	 * @throws \Cwola\Jsonc\Exception\JsoncException
	 */
	public function removeChild(AbstractNode $child) :AbstractNode {
		if ($this->isSameNode($child)) {
			throw new JsoncException('Node cannot be removed at the specified point in the hierarchy.');
		} else if (!$this->isParentOf($child)) {
			// $child is not childNode of $this.
			throw new JsoncException('Node Not Found.');
		}
        \array_splice($this->children, \array_search($child,$this->children,true), 1);
        $child->parent = null;
		return $child;
    }

    /**
     * @param void
     *
     * @return string
     */
    public function toReadable() :string {
        return $this->__toReadable(0);
    }

    /**
     * @param int $depth
     *
     * @return string
     */
    protected function __toReadable(int $depth) :string {
        $block = \str_repeat("    ", $depth);
        $readables = [];
        $readables[] = \sprintf(
            '%s->(type:%s, value:%s)',
            $block,
            static::class,
            isset($this->value) ? $this->value : ''
        );
        foreach ($this->children as $child) {
            $readables[] = $child->__toReadable($depth+1);
        }
        return \join("\n", $readables);
    }
}
