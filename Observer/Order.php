<?php

include_once __DIR__ . '/Observer.php';

class Order {
    private $observers = [];

    public function attach(Observer $observer) {
        $this->observers[] = $observer;
    }

    public function notify($data) {
        foreach ($this->observers as $observer) {
            $observer->update($data);
        }
    }

    public function complete() {
        $this->notify("Order Completed");
    }
}