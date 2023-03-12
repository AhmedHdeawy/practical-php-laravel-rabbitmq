<?php

require_once __DIR__ . './../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;


$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();


$exchangeName = 'direct_logs';
$routingKey = 'info';

$channel->exchange_declare($exchangeName, 'direct', false, false, false);
// Fanout means that all messages will send to all queues

$queue_name = "logs_queue_1";
$channel->queue_declare($queue_name, false, true, false, false);
$channel->queue_bind($queue_name, $exchangeName, $routingKey);


$callback = function ($msg) {
    $data = json_decode($msg->body, true);
    echo $data['id'] . ' Msg Received with name ' . $data['name'] . "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}


$channel->close();
$connection->close();
