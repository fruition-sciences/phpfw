<?php
error_reporting(E_ALL);
$autoloader = __DIR__ . '/vendor/autoload.php';
if (! file_exists($autoloader)) {
    echo "Autoloader not found: $autoloader" . PHP_EOL;
    echo "Please issue 'generate files' and try again." . PHP_EOL;
    exit(1);
}
require $autoloader;
Config::getInstance(true); //set test context
