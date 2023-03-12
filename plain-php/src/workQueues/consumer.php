<?php

require_once __DIR__ . './../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;


$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('log_q', false, true, false, false);

$callback = function ($msg) {
    $data = json_decode($msg->body, true);
    echo $data['id'] . ' Msg Received with name ' . $data['name'] . "\n";

    sleep($data['id']);

    $msg->ack();
};

$channel->basic_consume('log_q', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}