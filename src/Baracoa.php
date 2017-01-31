<?php
/**
 * This file is part of the Koriym\Baracoa package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Koriym\Baracoa;

use Koriym\Baracoa\Exception\JsFileNotExistsException;
use V8Js;

final class Baracoa
{
    /**
     * @var string
     */
    private $bundleSrcBasePath;

    /**
     * @var ExceptionHandler
     */
    private $handler;

    /*
     * @var V8Js
     */
    private $v8;

    /**
     * @param string                    $bundleSrcBasePath Bundled application code base dir path
     * @param ExceptionHandlerInterface $handler           V8JsScriptException exception handler
     * @param V8Js                      $v8Js              V8Js exception handler
     */
    public function __construct(string $bundleSrcBasePath, ExceptionHandlerInterface $handler, V8Js $v8Js)
    {
        $this->bundleSrcBasePath = $bundleSrcBasePath;
        $this->handler = $handler;
        $this->v8 = $v8Js;
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $appName, array $store, array $metas = []) : string
    {
        $bundleSrcPath = sprintf('%s/%s.bundle.js', $this->bundleSrcBasePath, $appName);
        if (! file_exists($bundleSrcPath)) {
            throw new JsFileNotExistsException($bundleSrcPath);
        }
        $bundleSrc = file_get_contents($bundleSrcPath);
        $code = $this->getSsrCode($bundleSrc, $store, $metas);
        try {
            $html = (string) $this->v8->executeString($code);
        } catch (\V8JsScriptException $e) {
            $handler = $this->handler;
            $html = $handler($e);
        }

        return $html;
    }

    private function getSsrCode($bundleSrc, array $store, array $metas) : string
    {
        $storeJson = json_encode($store);
        $metasJson = json_encode($metas);
        $code = <<< "EOT"
var console = {warn: function(){}, error: print};
var global = global || this, self = self || this, window = window || this;
window.__PRELOADED_STATE__ = {$storeJson};
window.__SSR_METAS__ = {$metasJson};
{$bundleSrc}
window.__SERVER_SIDE_MARKUP__;
EOT;

        return $code;
    }
}
