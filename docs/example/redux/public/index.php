<?php
/**
 * This file is part of the Koriym.Baracoa package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
require dirname(__DIR__, 4) . '/vendor/autoload.php';

use Koriym\Baracoa\Baracoa;
use Koriym\Baracoa\ExceptionHandler;

$jsBundleDir = __DIR__ . '/build';
$baracoa = new Baracoa($jsBundleDir, new ExceptionHandler(), new V8Js());
$state = ['hello' => ['name' => 'SSR']];
$metas = ['title' => '<page-title>'];
$html = $baracoa->render('index_ssr', $state, $metas);

echo $html;
