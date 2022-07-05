<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Converter;

use Cwola\Jsonc\Lexer\Handler as Lexer;

class JsonConverter implements Convertible {
    /**
     * @var bool
     */
    public bool $prettyPrint = false;

    /**
     * @var int
     */
    public int $depth = 512;

    /**
     * @var int
     */
    public int $decodeOptions = 0;


    /**
     * {@inheritDoc}
     */
    public function convert(string $Jsonc) :string {
        return $this->buildJson(
            $Jsonc,
            ((new Lexer)
                ->setConfig('stripComments', true)
                ->setConfig('stripWhiteSpaces', !$this->prettyPrint))
        );
    }

    /**
     * @param string $Jsonc
     * @param \Cwola\Jsonc\Lexer\Handler $lexer
     *
     * @return string
     *
     * @throws \Cwola\Jsonc\Exception\InternalException|\Cwola\Jsonc\Exception\SyntaxException
     */
    protected function buildJson(string $Jsonc, Lexer $lexer) :string {
        $json = '';
        foreach ($lexer->getTokens($Jsonc) as $token) {
            $json .= $token->text;
        }
        return $json;
    }
}
