<?php

include_once __DIR__ . '/Observer.php';

class EmailObserver implements Observer
{

    public function update($data)
    {
        echo "Send Email: $data\n";
    }
}