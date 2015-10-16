<?php

if (PHP_SAPI != 'cli') exit(1);

require_once 'vendor/autoload.php';

$application = new \Nitso\SqlConsole\Application('SQL Console', '');

$shell = new \Nitso\SqlConsole\Shell($application);
$shell->run();