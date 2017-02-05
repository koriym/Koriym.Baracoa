<?php

namespace Koriym\Baracoa;

use Symfony\Component\Cache\Simple\ArrayCache;

class CacheBaracoaTest extends BaracoaTest
{
    public function setUp()
    {
        $appBundleJsPath = dirname(__DIR__) . '/docs/example/redux/public/build/';
        $bundleFile = $appBundleJsPath . 'index_ssr.bundle.js';
        if (! file_exists($bundleFile)) {
            throw new \RuntimeException("{$bundleFile} is not build. See tests/README");
        }
        $this->baracoa = new CacheBaracoa($appBundleJsPath, new ExceptionHandler(), new ArrayCache());
    }

    public function testRender()
    {
        $state = ['hello' => ['name' => 'SSR']];
        $metas = ['title' => '<page-title>'];
        $html = $this->baracoa->render('index_ssr', $state, $metas);
        $html = $this->baracoa->render('index_ssr', $state, $metas);
        $this->assertContains('window.__PRELOADED_STATE__ = {"hello":{"name":"SSR"}}', $html);
        $this->assertContains('<div id="root"><div data-reactroot="" data-reactid="1" data-react-checksum=', $html);
        $this->assertContains('<!-- react-text: 3 -->Hello <!-- /react-text --><!-- react-text: 4 -->SSR<!-- /react-text -->', $html);
    }

    /**
     * cause "V8Js::createSnapshot(): Failed to create V8 heap snapshot.  Check $embed_source for errors." error
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testErrorCode()
    {
        $baracoa = new CacheBaracoa(__DIR__ . '/fake', new ExceptionHandler(), new ArrayCache());
        $baracoa->render('error', [], []);
    }
}