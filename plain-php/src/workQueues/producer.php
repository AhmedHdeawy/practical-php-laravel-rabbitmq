<?php


require_once __DIR__ . './../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('log_q', false, true, false, false);


for ($i=0; $i < 100; $i++) {

    $data = [
        'id' => $i,
        'name' => "Name #{$i}"
    ];

    $msg = new AMQPMessage(json_encode($data), array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));

    $channel->basic_publish($msg, '', 'log_q');


    echo " {$i} Message Sent \n \n \n \n \n";

    sleep(2);
}




$channel->close();
$connection->close();
