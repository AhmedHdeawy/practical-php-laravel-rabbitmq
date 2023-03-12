<?php


require_once __DIR__ . './../../vendor/autoload.php';


use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$exchangeName = 'direct_logs';
$channel->exchange_declare($exchangeName, 'direct', false, false, false);
// Fanout means that all messages will send to all queues

var_dump( $_ENV['AHMED']);
die;

for ($i=0; $i < 100; $i++) {
    $routingKey = 'info';
    if ($i % 2 == 0) {
        $routingKey = 'error';
    } elseif ($i % 3 == 0) {
        $routingKey = 'warning';
    }


    $data = ['id' => $i, 'name' => "Name #{$i}"];
    $msg = new AMQPMessage(json_encode($data));

    $channel->basic_publish($msg, $exchangeName, $routingKey);


    echo " {$i} Message Sent with Routing {$routingKey} \n \n \n \n \n";

    sleep(2);
}


$channel->close();
$connection->close();
