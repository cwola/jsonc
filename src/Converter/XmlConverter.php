<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Converter;

use DOMDocument;
use DOMElement;
use DOMComment;
use Cwola\Jsonc\Exception\JsoncException;
use Cwola\Jsonc\Lexer\Handler as Lexer;
use Cwola\Jsonc\Parser\Handler as Parser;
use Cwola\Jsonc\Structure\AST;
use Cwola\Jsonc\Structure\Node\AbstractNode;
use Cwola\Jsonc\Structure\Node\Comment;
use Cwola\Jsonc\Structure\Node\Map;
use Cwola\Jsonc\Structure\Node\Key;
use Cwola\Jsonc\Structure\Node\Value;
use Cwola\Jsonc\Structure\Node\NumValue;
use Cwola\Jsonc\Structure\Node\BoolValue;
use Cwola\Jsonc\Structure\Node\StrValue;
use Cwola\Jsonc\Structure\Node\NullValue;
use Cwola\Jsonc\Structure\Node\Set;

class XmlConverter implements Convertible {
    /**
     * @var bool
     */
    public bool $prettyPrint = false;


    /**
     * {@inheritDoc}
     *
     * @thrown
     */
    public function convert(string $Jsonc) :string {
        return $this->buildXml(
            $Jsonc,
            ((new Lexer())
                ->setConfig('stripComments', false)
                ->setConfig('stripWhiteSpaces', true))
        );
    }

    /**
     * @param string $Jsonc
     * @param \Cwola\Jsonc\Lexer\Handler $lexer
     *
     * @return string
     *
     * @thrown
     */
    protected function buildXml(string $Jsonc, Lexer $lexer) :string {
        $ast = (new Parser)->createAST($lexer->getTokens($Jsonc));
        return $this->compileToString($ast);
    }

    /**
     * @param \Cwola\Jsonc\Structure\AST $ast
     *
     * @return string
     *
     * @thrown
     */
    protected function compileToString(AST $ast) :string {
        $xml = $this->compile($ast);
        $xml->formatOutput = $this->prettyPrint;
        return $xml->saveXML();
    }

    /**
     * @param \Cwola\Jsonc\Structure\AST $ast
     *
     * @return \DOMDocument
     *
     * @throws \Cwola\Jsonc\Exception\JsoncException
     * @thrown
     */
    protected function compile(AST $ast) :DOMDocument {
        $xml = new DOMDocument('1.0', mb_internal_encoding());
        $root = $xml->createElement('Root');
        foreach ($ast->children as $child) {
            switch (true) {
                case $child instanceof Comment:
                    $this->appendComment($root, $child);
                    break;
                case $child instanceof Map:
                    $this->appendMap($root, $child);
                    break;
                case $child instanceof Value:
                    $this->appendValue($root, $child);
                    break;
                case $child instanceof Set:
                    $this->appendSet($root, $child);
                    break;
                default:
                    throw new JsoncException('Compile Error.');
            }
        }
        $xml->appendChild($root);
        return $xml;
    }

    /**
     * @param \DOMElement $parent
     * @param \Cwola\Jsonc\Structure\Node\Comment $comment
     *
     * @return \DOMComment
     */
    protected function appendComment(DOMElement $parent, Comment $comment) :DOMComment {
        $commentElement = new DOMComment($comment->children[0]->value);
        $parent->appendChild($commentElement);
        return $commentElement;
    }

    /**
     * @param \DOMElement $parent
     * @param \Cwola\Jsonc\Structure\Node\Map $map
     *
     * @return \DOMElement
     *
     * @throws \Cwola\Jsonc\Exception\JsoncException
     * @thrown
     */
    protected function appendMap(DOMElement $parent, Map $map) :DOMElement {
        $mapElement = new DOMElement('Map');
        $parent->appendChild($mapElement);
        foreach ($map->children as $child) {
            if ($child instanceof Comment) {
                $this->appendComment($mapElement, $child);
            } else if ($child instanceof Key) {
                $this->appendMapItem($mapElement, $child);
            } else {
                throw new JsoncException('Compile Error.');
            }
        }
        return $mapElement;
    }

    /**
     * @param \DOMElement $parent
     * @param \Cwola\Jsonc\Structure\Node\Set $set
     *
     * @return \DOMElement
     *
     * @throws \Cwola\Jsonc\Exception\JsoncException
     */
    protected function appendSet(DOMElement $parent, Set $set) :DOMElement {
        $setElement = new DOMElement('Set');
        $parent->appendChild($setElement);
        foreach ($set->children as $child) {
            if ($child instanceof Comment) {
                $this->appendComment($setElement, $child);
            } else if ($child instanceof Key) {
                throw new JsoncException('Compile Error.');
            } else {
                $this->appendSetItem($setElement, $child);
            }
        }
        return $setElement;
    }

    /**
     * @param \DOMElement $parent
     * @param \Cwola\Jsonc\Structure\Node\Key $key
     *
     * @return \DOMElement
     *
     * @throws \Cwola\Jsonc\Exception\JsoncException
     */
    protected function appendMapItem(DOMElement $parent, Key $key) :DOMElement {
        if ($parent->nodeName !== 'Map') {
            throw new JsoncException('Compile Error.');
        }
        $itemElement = new DOMElement('Item');
        $parent->appendChild($itemElement);
        $itemElement->setAttribute('key', $key->value);
        $cnt = 0;
        foreach ($key->children as $child) {
            if ($child instanceof Comment) {
                $this->appendComment($itemElement, $child);
            } else if ($child instanceof Map) {
                $this->appendMap($itemElement, $child);
                $itemElement->setAttribute('type', 'map');
                $cnt++;
            } else if ($child instanceof Set) {
                $this->appendSet($itemElement, $child);
                $itemElement->setAttribute('type', 'set');
                $cnt++;
            } else if ($child instanceof Value) {
                $this->putValue($itemElement, $child);
                $itemElement->setAttribute('type', $this->getValueType($child));
                $cnt++;
            } else {
                throw new JsoncException('Compile Error.');
            }
        }
        if ($cnt>1 || $cnt===0) {
            throw new JsoncException('Compile Error.');
        }
        return $itemElement;
    }

    /**
     * @param \DOMElement $parent
     * @param \Cwola\Jsonc\Structure\Node\AbstractNode $node
     *
     * @return \DOMElement
     *
     * @throws \Cwola\Jsonc\Exception\JsoncException
     */
    protected function appendSetItem(DOMElement $parent, AbstractNode $node) :DOMElement {
        if ($parent->nodeName !== 'Set') {
            throw new JsoncException('Compile Error.');
        }
        $itemElement = new DOMElement('Item');
        $parent->appendChild($itemElement);
        if ($node instanceof Map) {
            $this->appendMap($itemElement, $node);
            $itemElement->setAttribute('type', 'map');
        } else if ($node instanceof Set) {
            $this->appendSet($itemElement, $node);
            $itemElement->setAttribute('type', 'set');
        } else if ($node instanceof Value) {
            $this->putValue($itemElement, $node);
            $itemElement->setAttribute('type', $this->getValueType($node));
        } else {
            throw new JsoncException('Compile Error.');
        }
        return $itemElement;
    }

    /**
     * @param \DOMElement $parent
     * @param \Cwola\Jsonc\Structure\Node\Value $value
     *
     * @return \DOMElement
     */
    protected function appendValue(DOMElement $parent, Value $value) :DOMElement {
        $valueElement = new DOMElement('Value');
        $parent->appendChild($valueElement);
        $this->putValue($valueElement, $value);
        $valueElement->setAttribute('type', $this->getvalueType($value));
        return $valueElement;
    }

    /**
     * @param \DOMElement $parent
     * @param \Cwola\Jsonc\Structure\Node\Value $value
     *
     * @return string
     */
    protected function putValue(DOMElement $parent, Value $value) :string {
        $value = $this->valueRetrieve($value);
        $parent->nodeValue = $value;
        return $value;
    }

    /**
     * @param \Cwola\Jsonc\Structure\Node\Value $value
     *
     * @return string
     *
     * @throws \Cwola\Jsonc\Exception\JsoncException
     */
    protected function getvalueType(Value $value) :string {
        if ($value instanceof NumValue) {
            return 'number';
        } else if ($value instanceof BoolValue) {
            return 'boolean';
        } else if ($value instanceof StrValue) {
            return 'string';
        } else if ($value instanceof NullValue) {
            return 'null';
        } else {
            throw new JsoncException('Compile Error.');
        }
    }

    /**
     * @param \Cwola\Jsonc\Structure\Node\Value $value
     *
     * @return string
     */
    protected function valueRetrieve(Value $value) :string {
        if ($value instanceof BoolValue) {
            $value = $value->value ? 'true' : 'false';
        } else if ($value instanceof NullValue) {
            $value = 'null';
        } else {
            $value = \htmlspecialchars((string)$value->value, \ENT_XML1);
        }
        return $value;
    }
}
