<?php

/**
 * This file is part of the Koriym.Baracoa package.
 */

declare(strict_types=1);

namespace Koriym\Baracoa;

use Koriym\Baracoa\Exception\JsFileNotExistsException;
use Override;
use Psr\SimpleCache\CacheInterface;
use V8Js;
use V8JsScriptException;

use function file_exists;
use function file_get_contents;
use function json_encode;
use function sprintf;

final class CacheBaracoa implements BaracoaInterface
{
    public function __construct(
        private readonly string $bundleSrcBasePath,
        private readonly ExceptionHandlerInterface $handler,
        private readonly CacheInterface $cache,
    ) {
    }

    /** @inheritDoc */
    #[Override]
    public function render(string $appName, array $store, array $metas = []): string
    {
        if (! $this->cache->has($appName)) {
            $this->saveSnapshot($appName);
        }

        /** @var string $snapShot */
        $snapShot = $this->cache->get($appName);
        /** @psalm-suppress InvalidArgument */
        $v8 = new V8Js('PHP', [], $snapShot); // @phpstan-ignore argument.type
        try {
            /** @var string $html */
            $html = $v8->executeString($this->getSsrCode($store, $metas));
        } catch (V8JsScriptException $e) {
            $html = ($this->handler)($e);
        }

        return $html;
    }

    private function saveSnapshot(string $appName): void
    {
        $bundleSrcPath = sprintf('%s/%s.bundle.js', $this->bundleSrcBasePath, $appName);
        if (! file_exists($bundleSrcPath)) {
            throw new JsFileNotExistsException($bundleSrcPath);
        }

        $bundleSrc = (string) file_get_contents($bundleSrcPath);
        $snapShot = V8Js::createSnapshot($bundleSrc);
        $this->cache->set($appName, $snapShot);
    }

    /**
     * @param array<string, mixed> $store
     * @param array<string, mixed> $metas
     */
    private function getSsrCode(array $store, array $metas): string
    {
        $storeJson = json_encode($store);
        $metasJson = json_encode($metas);

        return <<<EOT
var console = {warn: function(){}, error: function(){}};
var global = global || this, self = self || this, window = window || this;
render($storeJson, $metasJson);
EOT;
    }
}
