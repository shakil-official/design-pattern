<?php

include_once __DIR__ . '/Observer.php';

class SMSObserver implements Observer
{
    public function update($data)
    {
        echo "Send SMS: $data\n";
    }
}