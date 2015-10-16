<?php

if (PHP_SAPI !== 'cli') die('Only console usage permitted');

require_once __DIR__ . '/../vendor/autoload.php';

$application = new \Nitso\SqlConsole\Application('SQL Console', '');

$shell = new \Nitso\SqlConsole\Shell($application);
$shell->run();