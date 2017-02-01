<?php
require dirname(__DIR__, 4) . '/vendor/autoload.php';

use Koriym\Baracoa\ExceptionHandler;
use Koriym\Baracoa\Baracoa;

$jsBundleDir = __DIR__ . '/build';
$baracoa = new Baracoa($jsBundleDir, new ExceptionHandler(), new V8Js());
$html = $baracoa->render('handlesbar', ['name' => 'Handlebar']);

echo $html;
