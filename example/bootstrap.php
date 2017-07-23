<?php
declare (strict_types=1);

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once __DIR__ . "/../vendor/autoload.php";

$config = Setup::createXMLMetadataConfiguration([
    __DIR__.'/../src/config/xml', // Library
    __DIR__.'/src/Blog/config/xml' // Example
], true);

// database configuration parameters
$connection = [
    'dbname' => 'sectionfield',
    'user' => 'root',
    'password' => 'eR83k1n8t0r',
    'host' => '127.0.0.1',
    'port' => '3306',
    'driver' => 'pdo_mysql'
];

// obtaining the entity manager
$entityManager = EntityManager::create($connection, $config);
