<?php
require dirname(__DIR__, 3) . '/vendor/autoload.php';

use Koriym\Baracoa\ExceptionHandler;
use Koriym\Baracoa\Baracoa;

$baracoa = new Baracoa(__DIR__, new ExceptionHandler());
$state = ['name' => 'World'];

echo $baracoa->render('min_ssr', $state) . PHP_EOL;
