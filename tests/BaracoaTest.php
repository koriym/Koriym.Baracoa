<?php

/**
 * This file is part of the Koriym.Baracoa package.
 */

declare(strict_types=1);

namespace Koriym\Baracoa;

use Koriym\Baracoa\Exception\JsFileNotExistsException;
use Override;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use V8Js;
use V8JsScriptException;

use function dirname;
use function extension_loaded;
use function file_exists;

class BaracoaTest extends TestCase
{
    protected BaracoaInterface $baracoa;

    #[Override]
    protected function setUp(): void
    {
        $appBundleJsPath = dirname(__DIR__) . '/docs/example/redux/public/build/';
        $bundleFile = $appBundleJsPath . 'index_ssr.bundle.js';
        if (! file_exists($bundleFile)) {
            throw new RuntimeException("{$bundleFile} is not build. See tests/README");
        }

        $v8js = extension_loaded('v8js') ? new V8Js() : null;
        $this->baracoa = new Baracoa($appBundleJsPath, new ExceptionHandler(), $v8js);
    }

    #[RequiresPhpExtension('v8js')]
    public function testNoJsFile(): void
    {
        $this->expectException(JsFileNotExistsException::class);
        $this->baracoa->render('__NOT_EXISTS__', [], []);
    }

    #[RequiresPhpExtension('v8js')]
    public function testInvoke(): void
    {
        $state = ['hello' => ['name' => 'SSR']];
        $metas = ['title' => '<page-title>'];
        $html = $this->baracoa->render('index_ssr', $state, $metas);
        $this->assertStringContainsString('window.__PRELOADED_STATE__ = {"hello":{"name":"SSR"}}', $html);
        $this->assertStringContainsString('<div id="root"><div data-reactroot="" data-reactid="1" data-react-checksum=', $html);
        $this->assertStringContainsString('<!-- react-text: 3 -->Hello <!-- /react-text --><!-- react-text: 4 -->SSR<!-- /react-text -->', $html);
    }

    #[RequiresPhpExtension('v8js')]
    public function testErrorCode(): void
    {
        $this->expectException(V8JsScriptException::class);
        $baracoa = new Baracoa(__DIR__ . '/fake', new ExceptionHandler(), new V8Js());
        $baracoa->render('error', [], []);
    }

    public function testPhpExecJs(): void
    {
        $appBundleJsPath = dirname(__DIR__) . '/docs/example/redux/public/build/';
        $this->baracoa = new Baracoa($appBundleJsPath, new ExceptionHandler(), null);
        $state = ['hello' => ['name' => 'SSR']];
        $metas = ['title' => '<page-title>'];
        $html = $this->baracoa->render('index_ssr', $state, $metas);
        $this->assertStringContainsString('window.__PRELOADED_STATE__ = {"hello":{"name":"SSR"}}', $html);
        $this->assertStringContainsString('<div id="root"><div data-reactroot="" data-reactid="1" data-react-checksum=', $html);
        $this->assertStringContainsString('<!-- react-text: 3 -->Hello <!-- /react-text --><!-- react-text: 4 -->SSR<!-- /react-text -->', $html);
    }
}
