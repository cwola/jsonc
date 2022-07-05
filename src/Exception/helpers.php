<?php

declare(strict_types=1);

namespace Cwola\Jsonc\Exception;

/**
 * create JsoncException.
 *
 * @param int $code
 * @param string $message
 *
 * @return JsoncException
 */
function convertJsonErrorToJsoncException(int $code, string $message) :JsoncException {
    switch($code) {
        case DepthException::CODE:                  return new DepthException($message);
        case StateMismatchException::CODE:          return new StateMismatchException($message);
        case CtrlCharException::CODE:               return new CtrlCharException($message);
        case SyntaxException::CODE:                 return new SyntaxException($message);
        case Utf8Exception::CODE:                   return new Utf8Exception($message);
        case RecursionException::CODE:              return new RecursionException($message);
        case InfOrNanException::CODE:               return new InfOrNanException($message);
        case UnsupportedTypeException::CODE:        return new UnsupportedTypeException($message);
        case InvalidPropertyNameException::CODE:    return new InvalidPropertyNameException($message);
        case Utf16Exception::CODE:                  return new Utf16Exception($message);
        default:                                    return new InternalException($message);
    }
}
