<?php

/**
 * This file is part of the Koriym.Baracoa package.
 */

declare(strict_types=1);

namespace Koriym\Baracoa;

use Override;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use V8JsScriptException;

use function dirname;
use function file_exists;

#[RequiresPhpExtension('v8js')]
class CacheBaracoaTest extends BaracoaTest
{
    #[Override]
    protected function setUp(): void
    {
        $appBundleJsPath = dirname(__DIR__) . '/docs/example/redux/public/build/';
        $bundleFile = $appBundleJsPath . 'index_ssr.bundle.js';
        if (! file_exists($bundleFile)) {
            throw new RuntimeException("{$bundleFile} is not build. See tests/README");
        }

        $this->baracoa = new CacheBaracoa($appBundleJsPath, new ExceptionHandler(), new Psr16Cache(new ArrayAdapter()));
    }

    public function testRender(): void
    {
        $state = ['hello' => ['name' => 'SSR']];
        $metas = ['title' => '<page-title>'];
        $html = $this->baracoa->render('index_ssr', $state, $metas);
        $html = $this->baracoa->render('index_ssr', $state, $metas);
        $this->assertStringContainsString('window.__PRELOADED_STATE__ = {"hello":{"name":"SSR"}}', $html);
        $this->assertStringContainsString('<div id="root"><div><h1>Hello <!-- -->SSR</h1>', $html);
    }

    #[Override]
    public function testErrorCode(): void
    {
        $this->expectException(V8JsScriptException::class);
        $baracoa = new CacheBaracoa(__DIR__ . '/fake', new ExceptionHandler(), new Psr16Cache(new ArrayAdapter()));
        $baracoa->render('error', [], []);
    }
}
