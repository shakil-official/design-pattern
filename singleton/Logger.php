<?php

namespace Singleton;

use Singleton\SingletonBase;

class Logger extends SingletonBase
{
    public $message = 'logger';

    public function log()
    {   
        echo $this->message . PHP_EOL;
    }
}
