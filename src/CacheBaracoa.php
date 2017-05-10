<?php
/**
 * This file is part of the Koriym.Baracoa package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Koriym\Baracoa;

use Koriym\Baracoa\Exception\JsFileNotExistsException;
use Psr\SimpleCache\CacheInterface;
use V8Js;

final class CacheBaracoa implements BaracoaInterface
{
    /**
     * @var string
     */
    private $bundleSrcBasePath;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var ExceptionHandlerInterface
     */
    private $handler;

    public function __construct(string $bundleSrcBasePath, ExceptionHandlerInterface $handler, CacheInterface $cache)
    {
        $this->bundleSrcBasePath = $bundleSrcBasePath;
        $this->handler = $handler;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $appName, array $store, array $metas = []) : string
    {
        if (! $this->cache->has($appName)) {
            $this->saveSnapshot($appName);
        }
        $snapShot = $this->cache->get($appName);
        $v8 = new V8Js('PHP', [], [], true, $snapShot);
        try {
            $html = $v8->executeString($this->getSsrCode($store, $metas));
        } catch (\V8JsScriptException $e) {
            $handler = $this->handler;
            $html = $handler($e);
        }

        return $html;
    }

    /**
     * @param string $appName
     */
    private function saveSnapshot(string $appName) : void
    {
        $bundleSrcPath = sprintf('%s/%s.bundle.js', $this->bundleSrcBasePath, $appName);
        if (! file_exists($bundleSrcPath)) {
            throw new JsFileNotExistsException($bundleSrcPath);
        }
        $bundleSrc = file_get_contents($bundleSrcPath);
        $snapShot = \V8Js::createSnapshot($bundleSrc);
        $this->cache->set($appName, $snapShot);
    }

    private function getSsrCode(array $store, array $metas) : string
    {
        $storeJson = json_encode($store);
        $metasJson = json_encode($metas);
        $code = <<< "EOT"
var console = {warn: function(){}, error: function(){}};
var global = global || this, self = self || this, window = window || this;
render($storeJson, $metasJson);
EOT;

        return $code;
    }
}
