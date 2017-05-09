<?php
/**
 * This file is part of the Koriym.Baracoa package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Koriym\Baracoa;

interface BaracoaInterface
{
    /**
     * Render by JS application
     *
     * @param string $appName JS app name "[$appName].bundle.js"
     * @param array  $store   initial state
     * @param array  $metas   meta data for renderer page
     *
     * @return string
     */
    public function render(string $appName, array $store, array $metas = []) : string;
}
