<?php

require_once __DIR__ . './../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function fib($n)
{
    if ($n == 0) {
        return 0;
    }
    if ($n == 1) {
        return 1;
    }
    return fib($n-1) + fib($n-2);
}


$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$rcpClientQueueToConsume = 'rpc_queue';
$channel->queue_declare($rcpClientQueueToConsume, false, true, false, false);

$callback = function ($request) {
    $data = json_decode($request->body, true);
    echo 'Msg Received with name: ' . $data['name'] . " And Request ID " . $request->get('correlation_id') . "\n";
    
    // Reply to the client through the given callback function
    $msg = new AMQPMessage(
        (string) fib($data['id']),
        [ 'correlation_id'    =>  $request->get('correlation_id') ]
    );

    $request->delivery_info['channel']->basic_publish($msg, '', $request->get('reply_to'));

    $request->ack();
};

$channel->basic_qos(0, 1, 0);
$channel->basic_consume($rcpClientQueueToConsume, '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}


$channel->close();
$connection->close();
