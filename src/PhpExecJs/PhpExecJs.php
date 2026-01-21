<?php

declare(strict_types=1);

namespace Koriym\Baracoa\PhpExecJs;

use RuntimeException;

/**
 * PHP JavaScript execution using external runtime (Node.js)
 */
final class PhpExecJs
{
    private readonly ExternalRuntime $runtime;

    public function __construct()
    {
        $this->runtime = new ExternalRuntime();
        if (! $this->runtime->isAvailable()) {
            throw new RuntimeException('PhpExecJs: Node.js runtime not found. Please install Node.js.');
        }
    }

    /**
     * Returns the name of the current runtime.
     */
    public function getRuntimeName(): string
    {
        return $this->runtime->getName();
    }

    /**
     * Evaluates JS code and returns the output.
     */
    public function evalJs(string $code): mixed
    {
        return $this->runtime->evalJs($code);
    }
}
