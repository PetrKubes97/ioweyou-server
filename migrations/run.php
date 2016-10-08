<?php

use Nextras\Migrations\Bridges;
use Nextras\Migrations\Controllers;
use Nextras\Migrations\Drivers;
use Nextras\Migrations\Extensions;
use Nette\Database\Connection;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/bootstrap.php';

//$conn = $container->getByType(Nette\Database\Connection::class);

$conn = new Nette\Database\Connection('mysql:host=127.0.0.1;dbname=payuback','root');

$dbal = new Bridges\NetteDatabase\NetteAdapter($conn);
// or	new Bridges\NextrasDbal\NextrasAdapter($conn);
// or   new Bridges\NetteDatabase\NetteAdapter($conn);
// or   new Bridges\DoctrineDbal\DoctrineAdapter($conn);
// or   new Bridges\Dibi\DibiAdapter($conn);

$driver = new Drivers\MySqlDriver($dbal);

$controller = new Controllers\HttpController($driver);
// or         new Controllers\ConsoleController($driver);
$baseDir = __DIR__;
$controller->addGroup('structures', "$baseDir/structures");
$controller->addGroup('basic-data', "$baseDir/basic-data", ['structures']);
$controller->addGroup('dummy-data', "$baseDir/dummy-data", ['basic-data']);
$controller->addExtension('sql', new Extensions\SqlHandler($driver));

$controller->run();
