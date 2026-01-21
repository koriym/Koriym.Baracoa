<?php

/**
 * This file is part of the Koriym.Baracoa package.
 */

declare(strict_types=1);

namespace Koriym\Baracoa;

use V8JsScriptException;

interface ExceptionHandlerInterface
{
    public function __invoke(V8JsScriptException $e): string;
}
