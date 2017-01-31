<?php
/**
 * This file is part of the Koriym\Baracoa package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Koriym\Baracoa;

final class ExceptionHandler implements ExceptionHandlerInterface
{
    public function __invoke(\V8JsScriptException $e) : string
    {
        $erroCode = mb_strimwidth($e->getJsSourceLine(), $e->getJsStartColumn(), 240, '...');
        $errorMsg = sprintf(
            "%s\n%s\nJS Stack trace:\n%s" ,
            $e->getMessage(),
            $erroCode,
            $e->getJsTrace()
        );
        throw new \V8JsScriptException($errorMsg);
    }
}
