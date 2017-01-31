<?php
require dirname(__DIR__, 4) . '/vendor/autoload.php';

use Koriym\Baracoa\ExceptionHandler;
use Koriym\Baracoa\Baracoa;

$jsBundleDir = __DIR__ . '/build';
$baracoa = new Baracoa($jsBundleDir, new ExceptionHandler(), new V8Js());
$state = ['hello' => ['name' => 'SSR']];
$metas = ['title' => '<page-title>'];
$html = $baracoa->render('index_ssr', $state, $metas);

echo $html;
