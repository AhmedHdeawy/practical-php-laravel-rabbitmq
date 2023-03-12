<?php


require_once __DIR__ . './../../vendor/autoload.php';


use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$exchangeName = 'logs';
$channel->exchange_declare($exchangeName, 'fanout', false, false, false);
// Fanout means that all messages will send to all queues



for ($i=0; $i < 100; $i++) {

    $data = [
        'id' => $i,
        'name' => "Name #{$i}"
    ];

    $msg = new AMQPMessage(json_encode($data));

    $channel->basic_publish($msg, $exchangeName);


    echo " {$i} Message Sent \n \n \n \n \n";

    sleep(2);
}




$channel->close();
$connection->close();
