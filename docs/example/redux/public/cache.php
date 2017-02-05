<?php
namespace Koriym\Baracoa;

require dirname(__DIR__, 4) . '/vendor/autoload.php';

use Symfony\Component\Cache\Simple\FilesystemCache;

$jsBundleDir = __DIR__ . '/build';
$baracoa = new CacheBaracoa($jsBundleDir, new ExceptionHandler(), new FilesystemCache());
$state = ['hello' => ['name' => 'SSR']];
$metas = ['title' => '<page-title>'];
$html = $baracoa->render('index_ssr', $state, $metas);

echo $html;
