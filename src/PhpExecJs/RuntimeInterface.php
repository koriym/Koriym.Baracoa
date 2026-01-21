<?php

declare(strict_types=1);

namespace Koriym\Baracoa\PhpExecJs;

interface RuntimeInterface
{
    /**
     * Evaluates JS code and returns the output.
     */
    public function evalJs(string $code): mixed;

    /**
     * Checks if the runtime is available.
     */
    public function isAvailable(): bool;

    /**
     * Returns the name of the runtime.
     */
    public function getName(): string;
}
