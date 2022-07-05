<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Lexer;

use Generator;

class Scanner implements Scannable {
    /**
     * @var string
     */
    protected string $regExp;

    /**
     * @param   string  $regExp
     */
    public function __construct(string $regExp) {
        $this->regExp = $regExp;
    }

    /**
     * {@inheritDoc}
     */
    public function scan(string $source) :Generator {
        $offset = 0;
        $matches = [];
        while (\preg_match(
            $this->regExp,
            $source,
            $matches,
            PREG_OFFSET_CAPTURE,
            $offset
        )===1) {
            $word = $matches[0][0];
            $offset = $matches[0][1] + \strlen($word);
            yield $word;
        }
    }
}
