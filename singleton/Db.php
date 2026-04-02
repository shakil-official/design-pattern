<?php

namespace Singleton;

class Db extends SingletonBase
{
    public $message = 'db';

    public function log()
    {
        echo $this->message . PHP_EOL;
    }

}