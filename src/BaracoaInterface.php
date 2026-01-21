<?php

/**
 * This file is part of the Koriym.Baracoa package.
 */

declare(strict_types=1);

namespace Koriym\Baracoa;

interface BaracoaInterface
{
    /**
     * Render by JS application
     *
     * @param array<string, mixed> $store initial state
     * @param array<string, mixed> $metas meta data for renderer page
     */
    public function render(string $appName, array $store, array $metas = []): string;
}
