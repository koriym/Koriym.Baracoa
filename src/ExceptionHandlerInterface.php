<?php
/**
 * This file is part of the Koriym.Baracoa package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Koriym\Baracoa;

interface ExceptionHandlerInterface
{
    public function __invoke(\V8JsScriptException $e) : string;
}
