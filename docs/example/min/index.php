<?php
/**
 * This file is part of the Koriym.Baracoa package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
require dirname(__DIR__, 3) . '/vendor/autoload.php';

use Koriym\Baracoa\Baracoa;
use Koriym\Baracoa\ExceptionHandler;

$baracoa = new Baracoa(__DIR__, new ExceptionHandler());
$state = ['name' => 'World'];

echo $baracoa->render('min_ssr', $state) . PHP_EOL;
