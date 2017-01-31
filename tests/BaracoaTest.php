<?php

namespace Koriym\Baracoa;

use Koriym\Baracoa\Exception\JsFileNotExistsException;
use V8Js;

class BaracoaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Baracoa
     */
    private $baracoa;

    public function setUp()
    {
        $appBundleJsPath = dirname(__DIR__) . '/docs/example/redux/public/build/';
        $bundleFile = $appBundleJsPath . 'index_ssr.bundle.js';
        if (! file_exists($bundleFile)) {
            throw new \RuntimeException("{$bundleFile} is not build. See tests/README");
        }
        $this->baracoa = new Baracoa($appBundleJsPath, new ExceptionHandler, new V8Js);
    }

    /**
     * @expectedException \Koriym\Baracoa\Exception\JsFileNotExistsException
     */
    public function testNoJsFile()
    {
        $this->baracoa->render('__NOT_EXISTS__', [], []);
    }

    public function testInvoke()
    {
        $state = ['hello' => ['name' => 'SSR']];
        $metas = ['title' => '<page-title>'];
        $html = $this->baracoa->render('index_ssr', $state, $metas);
        $this->assertContains('window.__PRELOADED_STATE__ = {"hello":{"name":"SSR"}}', $html);
        $this->assertContains('<div id="root"><div data-reactroot="" data-reactid="1" data-react-checksum=', $html);
        $this->assertContains('<!-- react-text: 3 -->Hello <!-- /react-text --><!-- react-text: 4 -->SSR<!-- /react-text -->', $html);
    }

    public function testInvalidReducerNameSpace()
    {
        $this->expectOutputString('Unexpected key "__INVALID__" found in preloadedState argument passed to createStore. Expected to find one of the known reducer keys instead: "hello". Unexpected keys will be ignored.');
        $state = ['__INVALID__' => ['message' => 'Hello SSR !']];
        $this->baracoa->render('index_ssr', $state, []);
    }

    public function testInvalidState()
    {
        $this->expectOutputRegex('/Warning: Failed prop type/');
        $state = ['hello' => ['__INVALID__' => 'Hello SSR !']];
        $this->baracoa->render('index_ssr', $state, []);
    }

    /**
     * @expectedException \V8JsScriptException
     */
    public function testErrorCode()
    {
        $baracoa = new Baracoa(__DIR__ . '/fake', new ExceptionHandler(), new V8Js());
        $html = $baracoa->render('error', [], []);
    }
}
