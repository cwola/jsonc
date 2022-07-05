<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Parser;

use Generator;
use Cwola\Jsonc\Structure\Token;
use Cwola\Jsonc\Structure\Node\AbstractNode;
use Cwola\Jsonc\Structure\Node\Comment;
use Cwola\Jsonc\Structure\Node\Map;
use Cwola\Jsonc\Structure\Node\Key;
use Cwola\Jsonc\Structure\Node\NumValue;
use Cwola\Jsonc\Structure\Node\BoolValue;
use Cwola\Jsonc\Structure\Node\StrValue;
use Cwola\Jsonc\Structure\Node\NullValue;
use Cwola\Jsonc\Structure\Node\Set;
use Cwola\Jsonc\Structure\AST;

class Handler {
    /**
     * create AbstractSyntaxTree.
     *
     * @param \Generator<\Cwola\Jsonc\Structure\Token>|\Cwola\Jsonc\Structure\Token[] $tokenStream
     *
     * @return \Cwola\Jsonc\Structure\AST
     */
    function createAST(Generator|array $tokenStream) :AST {
        $node = new AST;
        foreach ($tokenStream as $token) {
            $node = $this->apply($node, $token);
        }
        return $node;
    }

    /**
     * @param \Cwola\Jsonc\Structure\Node\AbstractNode $node
     * @param \Cwola\Jsonc\Structure\Token $token
     *
     * @return \Cwola\Jsonc\Structure\Node\AbstractNode
     */
    protected function apply(AbstractNode $node, Token $token) :AbstractNode {
        switch ($token->id) {
            case Token::T_LINE_COMMENT:
            case Token::T_DOC_COMMENT:
                $node->appendChild(new Comment())
                        ->appendChild(new StrValue($this->stripCommentKeyword($token->text)));
                break;
            case Token::T_NUMBER:
                $node->appendChild(new NumValue((float)$token->text));
                break;
            case Token::T_BOOLEAN:
                $node->appendChild(new BoolValue(($token->text==='true')?true:false));
                break;
            case Token::T_STRING:
                $text = $token->plainText();
                if ($node instanceof Map) {
                    $node = $node->appendChild(new Key($text)); // $node = Key Node.
                } else {
                    $node->appendChild(new StrValue($text));
                }
                break;
            case Token::T_NULL:
                $node->appendChild(new NullValue());
                break;
            case Token::T_LBRACE:
                $node = $node->appendChild(new Map());  // $node = Map Node.
                break;
            case Token::T_LBRACKET:
                $node = $node->appendChild(new Set());  // $node = Set Node.
                break;
            case Token::T_RBRACE:
            case Token::T_RBRACKET:
                if ($node instanceof Key) {
                    $node = $node->parent;
                }
                $node = $node->parent;
                break;
            case Token::T_COMMA:
                if ($node instanceof Key) {
                    $node = $node->parent;
                }
                break;
            default:
                break;
        }
        return $node;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    protected function stripCommentKeyword(string $str) :string {
        if (\preg_match("/^\/\*/", $str)===1) {
            $str = \preg_replace("/^\/\*/",'',\preg_replace("/\*\/$/",'',$str));
        } else if (\preg_match("/^\/\//", $str)===1) {
            $str = \preg_replace("/^\/\//",'',$str);
        }
        return $str;
    }
}
