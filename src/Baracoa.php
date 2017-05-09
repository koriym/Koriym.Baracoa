<?php
/**
 * This file is part of the Koriym.Baracoa package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Koriym\Baracoa;

use Koriym\Baracoa\Exception\JsFileNotExistsException;
use Nacmartin\PhpExecJs\PhpExecJs;
use V8Js;

final class Baracoa implements BaracoaInterface
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

    private $execJs;

    /**
     * @param string                    $bundleSrcBasePath Bundled application code base dir path
     * @param ExceptionHandlerInterface $handler           V8JsScriptException exception handler
     * @param V8Js                      $v8Js              V8Js exception handler
     */
    public function __construct(string $bundleSrcBasePath, ExceptionHandlerInterface $handler, V8Js $v8Js = null)
    {
        $this->bundleSrcBasePath = $bundleSrcBasePath;
        $this->handler = $handler;
        $this->v8 = $v8Js;
        if ($v8Js === null) {
            $this->execJs = new PhpExecJs();
        }
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
            $html = $this->execCode($code);
        } catch (\V8JsScriptException $e) {
            $handler = $this->handler;
            $html = $handler($e);
        }

        return $html;
    }

    private function execCode(string $code) : string
    {
        if ($this->v8 instanceof V8Js) {
            return (string) $this->v8->executeString($code);
        }

        return (string) $this->execJs->evalJs($code);
    }

    private function getSsrCode($bundleSrc, array $store, array $metas) : string
    {
        $storeJson = json_encode($store);
        $metasJson = json_encode($metas);
        $code = <<< "EOT"
var console = {warn: function(){}, error: function(){}};
var global = global || this, self = self || this, window = window || this;
{$bundleSrc}
render($storeJson, $metasJson);
EOT;

        return $code;
    }
}
