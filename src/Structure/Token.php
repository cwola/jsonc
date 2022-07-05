<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Structure;

use ReflectionClass;

class Token {

    /**
     * @var int HASH_COMMENT
     * #
     */
    //const T_HASH_COMMENT = 1;

    /**
     * @var int LINE_COMMENT
     * //
     */
    const T_LINE_COMMENT = 2;

    /**
     * @var int DOC_COMMENT
     */
    const T_DOC_COMMENT = 3;

    /**
     * @var int NUMBER
     * integer or float.
     */
    const T_NUMBER = 10;

    /**
     * @var int BOOLEAN
     * true or false.
     */
    const T_BOOLEAN = 20;

    /**
     * @var int STRING
     * string.
     */
    const T_STRING = 30;

    /**
     * @var int NULL
     * null.
     */
    const T_NULL = 90;

    /**
     * @var int LBRACE
     * {
     */
    const T_LBRACE = 100;

    /**
     * @var int RBRACE
     * }
     */
    const T_RBRACE = 110;

    /**
     * @var int LBRACKET
     * [
     */
    const T_LBRACKET = 150;

    /**
     * @var int RBRACKET
     * ]
     */
    const T_RBRACKET = 160;

    /**
     * @var int COMMA
     * ,
     */
    const T_COMMA = 180;

    /**
     * @var int COLON
     * :
     */
    const T_COLON = 190;

    /**
     * @var int CR
     * \r
     */
    const T_CR = 200;

    /**
     * @var int LF
     * \n
     */
    const T_LF = 201;

    /**
     * @var int WHITE_SPACE
     * [space]
     */
    const T_WHITE_SPACE = 250;

    /**
     * @var int BOF
     * Beginning Of File.
     */
    const T_BOF = 0;

    /**
     * @var int EOF
     * End Of File.
     */
    const T_EOF = 999;


    /**
     * @var int token ID.
     */
    public int $id;

    /**
     * @var string token name.
     */
    public string $name;

    /**
     * @var string token string.
     */
    public string $text;

    /**
     * @var int line.
     */
    public int $line;


    /**
     * @param int $id
     * @param string $text
     * @param int $line [optional]
     */
    public function __construct(int $id, string $text, int $line = -1) {
        $this->id = $id;
        $this->name = $this->getTokenName($id);
        $this->text = $text;
        $this->line = $line;
    }

    /**
     * @param void
     *
     * @return string
     */
    public function __toString() :string {
        return $this->text;
    }

    /**
     * @param void
     *
     * @return bool
     */
    public function isComment() :bool {
        $haystack = [
            static::T_LINE_COMMENT,
            static::T_DOC_COMMENT
        ];
        return \in_array($this->id, $haystack, true);
    }

    /**
     * @param bool $includeBr [optional]
     *
     * @return bool
     */
    public function isSpace(bool $includeBr = false) :bool {
        $haystack = [static::T_WHITE_SPACE];
        return \in_array($this->id, $haystack, true) || $includeBr && $this->isBr();
    }

    /**
     * @param void
     *
     * @return bool
     */
    public function isBr() :bool {
        $haystack = [
            static::T_CR,
            static::T_LF
        ];
        return \in_array($this->id, $haystack, true);
    }

    /**
     * @param bool $includeNull [optional]
     *
     * @return bool
     */
    public function isValue(bool $includeNull = false) :bool {
        $haystack = [
            static::T_NUMBER,
            static::T_BOOLEAN,
            static::T_STRING
        ];
        return \in_array($this->id, $haystack, true) || $includeNull && $this->isNull();
    }

    /**
     * @param void
     *
     * @return bool
     */
    public function isNull() :bool {
        return $this->id === static::T_NULL;
    }

    /**
     * @param void
     *
     * @return string
     */
    public function plainText() :string {
        if ($this->id === static::T_STRING) {
            return \json_decode($this->text);
        }
        return $this->text;
    }

    /**
     * @param bool $plain [optional]
     *
     * @return string
     */
    public function toReadable(bool $plain = false) :string {
        return \sprintf(
            '[%s] : %s',
            $this->name,
            $plain ? $this->plainText() : $this->text
        );
    }

    /**
     * @param int $id
     *
     * @return string?
     */
    protected function getTokenName(int $id) :?string {
        $reflection = new ReflectionClass(__CLASS__);
        foreach ($reflection->getConstants() as $name => $value) {
            if ($value===$id ) {
                return $name;
            }
        }
        return null;
    }
}
