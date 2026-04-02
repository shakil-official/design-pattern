<?php

namespace Singleton;

/**
 * SingletonBase class is a base class for all singleton classes
 * using late static binding
 * why we use late static binding? because we can't use self:: in this case
 * using get_called_class() method to get the name of the class that is calling the method and extends this class
 * 
 * @package Singleton
 * @author Shakil
 * @version 1.0.0

 * */

class SingletonBase
{

    // make private static instance reason is to store single instance
    protected static $instance = [];

    // make pro tected constructor because we can't create instance from outside
    protected final function __construct()
    {
    }
    // make protected clone method to prevent cloning
    protected final function __clone()
    {
    }

    // make public wakeup method that throws an exception to prevent unserialization
    protected final function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }


    /**
     * @return static
     */
    public static function getInstance()
    {
        // get_called_class() is used to get the name of the class that is calling the method
        $class = get_called_class();

        if (!isset(static::$instance[$class])) {
            static::$instance[$class] = new static();
        }

        return static::$instance[$class];
    }
}
