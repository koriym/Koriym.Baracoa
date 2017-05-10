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
$baracoa = new Baracoa($jsBundleDir, new ExceptionHandler());
$html = $baracoa->render('handlesbar', ['name' => 'Handlebar']);

echo $html;
