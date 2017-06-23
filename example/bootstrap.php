<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "../vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../sectionfield/src/Entities"), $isDevMode);
// or if you prefer yaml or XML
$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/../sectionfield/src/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters
$conn = array(
    'dbname' => 'sectionfield',
    'user' => 'root',
    'password' => 'eR83k1n8t0r',
    'host' => '127.0.0.1',
    'port' => '3306',
    'driver' => 'pdo_mysql'
);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);
