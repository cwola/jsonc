<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Lexer;

use Generator;
use Cwola\Jsonc\Structure\Token;

class Handler {

    /**
     * @var int[]
     *
     * $whiteSpaces is changeable.
     */
    public array $whiteSpaces = [
        Token::T_CR,
        Token::T_LF,
        Token::T_WHITE_SPACE
    ];

    /**
     * @var int[]
     *
     * $comments is changeable.
     */
    public array $comments = [
        //Token::T_HASH_COMMENT,
        Token::T_LINE_COMMENT,
        Token::T_DOC_COMMENT
    ];

    /**
     * @var array
     */
    protected array $configs = [
        'stripComments'     =>  false,
        'stripWhiteSpaces'  =>  false
    ];

    /**
     * @var \Cwola\Jsonc\Lexer\Scannable
     */
    protected Scannable $scanner;

    /**
     * @var \Cwola\Jsonc\Lexer\Tokenizable
     */
    protected Tokenizable $tokenizer;


    /**
     * @param \Cwola\Jsonc\Lexer\Scannable? $scanner [optional]
     * @param \Cwola\Jsonc\Lexer\Tokenizable? $tokenizer [optional]
     */
    public function __construct(?Scannable $scanner = null, ?Tokenizable $tokenizer = null) {
        $splitter = "/((?:\/\/)[^\r\n]*|\/\*[\s|\S]*?\*\/|\"(?:\\\\.|[^\"\\\\])*\"|,|-?[0-9]+(?:\.[0-9]+)?|(?:true|false)|null|:|\r|\n|{|}|\[|\]|[^\S\r\n]+|[\S]+?)/";
        $this->scanner = $scanner ?: (new Scanner($splitter));
        $this->tokenizer = $tokenizer ?: (new Tokenizer());
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     */
    public function setConfig(string $name, mixed $value) :static {
        $this->configs[$name] = $value;
        return $this;
    }

    /**
     * @param string $source
     *
     * @return \Generator<\Cwola\Jsonc\Structure\Token>
     *
     * @thrown
     */
    public function getTokens(string $source) :Generator {
        $ignoreTokens = $this->makeIgnoreTokens();
        $line = 1;
        foreach ($this->scanner->scan($source) as $word) {
            $token = $this->tokenizer->tokenize($word);
            $token->line = $line;
            $line += \substr_count($word, "\n");
            if (\in_array($token->id, $ignoreTokens, true)===true) {
                continue;
            }
            yield $token;
        }
    }

    /**
     * @param void
     *
     * @return int[]
     */
    protected function makeIgnoreTokens() :array {
        $ignoreTokens = [];
        if ((bool)$this->configs['stripComments']===true) {
            $ignoreTokens = \array_merge($ignoreTokens, $this->comments);
        }
        if ((bool)$this->configs['stripWhiteSpaces']===true) {
            $ignoreTokens = \array_merge($ignoreTokens, $this->whiteSpaces);
        }
        return $ignoreTokens;
    }
}
