<?php

require_once __DIR__ . '/singleton/Database.php';
require_once __DIR__ . '/singleton/SingletonBase.php';
require_once __DIR__ . '/singleton/Logger.php';
require_once __DIR__ . '/singleton/Db.php';

use Singleton\Database;
use Singleton\Logger;
use Singleton\Db;




/* Singleton Pattern Example start */

// getInstance() method is used to get the instance of the class
// using :: (scope resolution operator) is called static method
$firstData = Database::getInstance();
$secondData = Database::getInstance();

// $secondData = clone $firstData; // uncomment that for check clone 
// serialization and unserialization
// $serializedData = serialize($firstData);
// $unserializedData = unserialize($serializedData);


//checking both are same instance using === operator
var_dump($firstData === $secondData);



$log = Logger::getInstance();
$db = Db::getInstance();

$log->log();
$db->log();

var_dump($log === $db);


/* Singleton Pattern Example end */