<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Lexer;

use Generator;

interface Scannable {
    /**
     * @param string $source
     *
     * @return \Generator<string>
     */
    public function scan(string $source) :Generator;
}
