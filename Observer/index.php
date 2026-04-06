<?php

include_once __DIR__ . '/Order.php';
include_once __DIR__ . '/EmailObserver.php';
include_once __DIR__ . '/SMSObserver.php';


/* Observer Pattern Example start */

$order = new Order();
$order->attach(new EmailObserver());
$order->attach(new SMSObserver());
// same as SMS observer or others can add 

$order->complete();

/* Observer Pattern Example end */
