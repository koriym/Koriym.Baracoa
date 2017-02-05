<?php
require dirname(__DIR__, 4) . '/vendor/autoload.php';

use Koriym\Baracoa\ExceptionHandler;
use Koriym\Baracoa\Baracoa;

$jsBundleDir = __DIR__ . '/build';

$cache = new \Cache\Adapter\Filesystem\FilesystemCachePool(new \League\Flysystem\Filesystem(new \League\Flysystem\Adapter\Local(__DIR__ . '/')));
$baracoa = new \Koriym\Baracoa\CacheBaracoa($jsBundleDir, new ExceptionHandler(), $cache);
$state = ['hello' => ['name' => 'SSR']];
$metas = ['title' => '<page-title>'];
$html = $baracoa->render('index_ssr', $state, $metas);

echo $html;
