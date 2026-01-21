<?php

/**
 * This file is part of the Koriym.Baracoa package.
 */

declare(strict_types=1);

namespace Koriym\Baracoa;

use Koriym\Baracoa\Exception\JsFileNotExistsException;
use Koriym\Baracoa\PhpExecJs\PhpExecJs;
use Override;
use V8Js;
use V8JsScriptException;

use function assert;
use function file_exists;
use function file_get_contents;
use function json_encode;
use function sprintf;

final class Baracoa implements BaracoaInterface
{
    private V8Js|null $v8;
    private PhpExecJs|null $execJs = null;

    public function __construct(
        private readonly string $bundleSrcBasePath,
        private readonly ExceptionHandlerInterface $handler,
        V8Js|null $v8Js = null,
    ) {
        $this->v8 = $v8Js;
        if ($v8Js === null) {
            $this->execJs = new PhpExecJs();
        }
    }

    /** @inheritDoc */
    #[Override]
    public function render(string $appName, array $store, array $metas = []): string
    {
        $bundleSrcPath = sprintf('%s/%s.bundle.js', $this->bundleSrcBasePath, $appName);
        if (! file_exists($bundleSrcPath)) {
            throw new JsFileNotExistsException($bundleSrcPath);
        }

        $bundleSrc = (string) file_get_contents($bundleSrcPath);
        $code = $this->getSsrCode($bundleSrc, $store, $metas);
        try {
            $html = $this->execCode($code);
        } catch (V8JsScriptException $e) {
            $html = ($this->handler)($e);
        }

        return $html;
    }

    private function execCode(string $code): string
    {
        if ($this->v8 instanceof V8Js) {
            /** @var string $result */
            $result = $this->v8->executeString($code);

            return $result;
        }

        assert($this->execJs instanceof PhpExecJs);
        /** @var string $result */
        $result = $this->execJs->evalJs($code);

        return $result;
    }

    /**
     * @param array<string, mixed> $store
     * @param array<string, mixed> $metas
     */
    private function getSsrCode(string $bundleSrc, array $store, array $metas): string
    {
        $storeJson = json_encode($store);
        $metasJson = json_encode($metas);

        return <<<EOT
var console = {warn: function(){}, error: function(){}};
var global = global || this, self = self || this, window = window || this;
{$bundleSrc}
render($storeJson, $metasJson);
EOT;
    }
}
