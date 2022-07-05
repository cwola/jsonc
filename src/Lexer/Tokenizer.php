<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Lexer;

use Cwola\Jsonc\Structure\Token;

class Tokenizer implements Tokenizable {
    /**
     * {@inheritDoc}
     *
     * @thrown
     */
    public function tokenize(string $word) :Token {
        return (new Token($this->decisionTokenType($word), $word));
    }

    /**
     * @param string $word
     *
     * @return int
     *
     * @throws \Cwola\Jsonc\Lexer\TokenizerException
     */
    protected function decisionTokenType(string $word) :int {
        $head = \substr($word, 0, 1);
        $tokenId = null;
        switch ($head) {
            //case '#':
            //    $tokenId = Token::T_HASH_COMMENT; break;
            case '/':
                $head = \substr($word, 1, 1);
                if ($head==='/') $tokenId = Token::T_LINE_COMMENT;
                else if ($head==='*') $tokenId = Token::T_DOC_COMMENT;
                break;
            case '"':
                $tokenId = Token::T_STRING; break;
            case ',':
                $tokenId = Token::T_COMMA; break;
            case ':':
                $tokenId = Token::T_COLON; break;
            case '{':
                $tokenId = Token::T_LBRACE; break;
            case '}':
                $tokenId = Token::T_RBRACE; break;
            case '[':
                $tokenId = Token::T_LBRACKET; break;
            case ']':
                $tokenId = Token::T_RBRACKET; break;
            case "\r":
                $tokenId = Token::T_CR; break;
            case "\n":
                $tokenId = Token::T_LF; break;
            default:
                if (\is_numeric($word)===true) $tokenId = Token::T_NUMBER;
                else if (\in_array($word, ['true', 'false'], true)===true) $tokenId = Token::T_BOOLEAN;
                else if ($word==='null') $tokenId = Token::T_NULL;
                else if (\trim($word)==='') $tokenId = Token::T_WHITE_SPACE;
        }
        if (\is_int($tokenId)===false) {
            throw new TokenizerException('Unexpected token( ' . $word . ' ).');
        }
        return $tokenId;
    }
}
