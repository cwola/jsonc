<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Converter;

interface Convertible {
    /**
     * @param string $Jsonc
     *
     * @return string
     */
    public function convert(string $Jsonc) :string;
}
