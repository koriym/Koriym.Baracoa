<?php

/**
 * This file is part of the Koriym.Baracoa package.
 */

declare(strict_types=1);

namespace Koriym\Baracoa;

use Override;
use V8JsScriptException;

use function mb_strimwidth;
use function sprintf;

final class ExceptionHandler implements ExceptionHandlerInterface
{
    #[Override]
    public function __invoke(V8JsScriptException $e): string
    {
        $erroCode = mb_strimwidth($e->getJsSourceLine(), $e->getJsStartColumn(), 240, '...');
        $errorMsg = sprintf(
            "%s\n%s\nJS Stack trace:\n%s",
            $e->getMessage(),
            $erroCode,
            $e->getJsTrace(),
        );

        throw new V8JsScriptException($errorMsg);
    }
}
