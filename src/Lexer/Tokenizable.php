<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Lexer;

use Cwola\Jsonc\Structure\Token;

interface Tokenizable {
    /**
     * @param string $word
     *
     * @return \Cwola\Jsonc\Structure\Token
     */
    public function tokenize(string $word) :Token;
}
