<?php

use App\Connection\DriverBridge;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

// replace with file to your own project bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src/Entity"), true);

$connection = new Connection([
    'host' => 'mysql',
    'user' => 'root',
    'password' => 'root',
    'dbname' => 'test',
], new DriverBridge());

// obtaining the entity manager
return EntityManager::create($connection, $config);
