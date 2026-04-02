<?php

namespace Singleton;

class Database
{
    // make private static instance reason is to store single instance
    private static $instance = null;


    // make private constructor because we can't create instance from outside
    private function __construct()
    {

    }


    // make public static method to get instance for 
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();}

        return self::$instance;
    }

    // make private clone method to prevent cloning
    private function __clone()
    {

    }

    // make public wakeup method that throws an exception to prevent unserialization
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

}




