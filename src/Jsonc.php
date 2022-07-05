<?php

declare(strict_types=1);

namespace Cwola\Jsonc;

use function Cwola\Jsonc\Exception\convertJsonErrorToJsoncException;
use Generator;
use Cwola\Jsonc\Exception\InvalidParameterException;
use Cwola\Jsonc\Exception\InternalException;
use Cwola\Jsonc\Structure\AST;
use Cwola\Jsonc\Lexer\Handler as Lexer;
use Cwola\Jsonc\Parser\Handler as Parser;
use Cwola\Jsonc\Converter\JsonConverter;
use Cwola\Jsonc\Converter\XmlConverter;


/**
 * @var int
 */
const STRIP_COMMENTS = 1;

/**
 * @var int
 */
const STRIP_WHITE_SPACES = 2;


/**
 * @param string $source JSON or JSONC string.
 * @param bool $assoc [optional]
 * @param int $depth [optional]
 * @param int $options [optional]
 *
 * @return mixed
 *
 * @thrown
 *
 * @link https://www.php.net/manual/en/function.json-decode.php
 */
function decode(string $source, bool $assoc = false, int $depth = 512, int $options = 0) :mixed {
    $ret = \json_decode(toJson($source, false, ['depth'=>$depth, 'decodeOptions'=>$options]), $assoc, $depth, $options);
    ensureLastProcess();
    return $ret;
}

/**
 * @param string $filePath JSON or JSONC file path.
 * @param bool $assoc [optional]
 * @param int $depth [optional]
 * @param int $options [optional]
 *
 * @return mixed
 *
 * @throws \Cwola\Jsonc\Exception\InvalidParameterException|\Cwola\Jsonc\Exception\InternalException
 * @thrown
 */
function decodeByFile(string $filePath, bool $assoc = false, int $depth = 512, int $options = 0) :mixed {
    if (\is_file($filePath)===false && \is_readable($filePath)===false) {
        throw new InvalidParameterException('Either '.$filePath.' is not a file or you do not have read permission.');
    }
    $json = \file_get_contents($filePath);
    if (\is_string($json)===false) {
        throw new InternalException('Failed to read '.$filePath.'.');
    }
    return decode($json, $assoc, $depth, $options);
}
        
/**
 * @param mixed $value
 * @param int $options [optional]
 * @param int $depth [optional]
 *
 * @return string JSON.
 *
 * @thrown
 *
 * @link https://www.php.net/manual/en/function.json-encode.php
 */
function encode(mixed $value, int $options = 0, int $depth = 512) :string {
    $ret = \json_encode($value, $options, $depth);
    ensureLastProcess();
    return $ret;
}


/**
 * @param string $source
 * @param int $options [optional]
 *
 * @return \Generator<\Cwola\Jsonc\Structure\Token>
 *
 * @thrown
 */
function getTokens(string $source, int $options = 0) :Generator {
    return ((new Lexer())
        ->setConfig('stripComments', (bool)($options & STRIP_COMMENTS))
        ->setConfig('stripWhiteSpaces', (bool)($options & STRIP_WHITE_SPACES))
        ->getTokens($source));
}

/**
 * @param string $source
 * @param int $options [optional]
 *
 * @return \Cwola\Jsonc\Structure\AST
 *
 * @thrown
 */
function getAST(string $source, int $options = 0) :AST {
    return (new Parser())->createAST(getTokens($source, $options));
}


/**
 * @param string $Jsonc
 * @param bool $prettyPrint [optional]
 * @param array $options [optional]
 *
 * @return string
 *
 * @thrown
 */
function toJson(string $Jsonc, bool $prettyPrint = false, array $options = []) :string {
    if (\preg_match('/(\/\/|\/\*|@)/', $Jsonc)===1) {
        $converter = new JsonConverter;
        $converter->prettyPrint = $prettyPrint;
        if (isset($options['depth'])) {
            $converter->depth = $options['depth'];
        }
        if (isset($options['decodeOptions'])) {
            $converter->decodeOptions = $options['decodeOptions'];
        }
        return $converter->convert($Jsonc);
    }
    return $Jsonc;
}

/**
 * @param string $Jsonc
 * @param bool $prettyPrint [optional]
 *
 * @return string
 *
 * @thrown
 */
function toXml(string $Jsonc, bool $prettyPrint = false) :string {
    $converter = new XmlConverter;
    $converter->prettyPrint = $prettyPrint;
    return $converter->convert($Jsonc);
}

/**
 * @param string $Jsonc
 *
 * @return string token list.
 *
 * @thrown
 */
function toReadableTokens(string $Jsonc) :string {
    $tokenlist = [];
    foreach (getTokens($Jsonc) as $token) {
        $tokenlist[] = $token->toReadable(plain:false);
    }
    return \join("\n", $tokenlist);
}

/**
 * @param string $Jsonc
 *
 * @return string AST string.
 *
 * @thrown
 */
function toReadableAST(string $Jsonc) :string {
    return getAST($Jsonc)->toReadable();
}


/**
 * @return void
 *
 * @throws \Cwola\Jsonc\Exception\JsoncException
 */
function ensureLastProcess() :void {
    if (\json_last_error()!==JSON_ERROR_NONE) {
        throw convertJsonErrorToJsoncException(\json_last_error(), \json_last_error_msg());
    }
}
